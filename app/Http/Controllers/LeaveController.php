<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * My leave list.
     */
    public function index()
    {
        $leaves = Leave::forUser(Auth::id())
            ->with('approver')
            ->latest()
            ->paginate(15);

        $leaveCounts = [
            'L'  => Leave::forUser(Auth::id())->approved()->where('leave_type', 'L')->sum('total_days'),
            'SL' => Leave::forUser(Auth::id())->approved()->where('leave_type', 'SL')->sum('total_days'),
            'H'  => Leave::forUser(Auth::id())->approved()->where('leave_type', 'H')->sum('total_days'),
            'CO' => Leave::forUser(Auth::id())->approved()->where('leave_type', 'CO')->sum('total_days'),
        ];

        return view('leaves.index', compact('leaves', 'leaveCounts'));
    }

    /**
     * Leave request form.
     */
    public function create()
    {
        $leaveTypes = Leave::$leaveTypes;
        return view('leaves.create', compact('leaveTypes'));
    }

    /**
     * Submit leave request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:L,SL,H,CO',
            'from_date'  => 'required|date|after_or_equal:today',
            'to_date'    => 'required|date|after_or_equal:from_date',
            'reason'     => 'required|string|max:1000',
        ]);

        $from  = Carbon::parse($validated['from_date']);
        $to    = Carbon::parse($validated['to_date']);
        $total = 0;

        // Count working days (exclude weekends)
        $current = $from->copy();
        while ($current->lte($to)) {
            if (!$current->isWeekend()) {
                $total++;
            }
            $current->addDay();
        }

        // Check for overlapping leave requests
        $overlap = Leave::forUser(Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->where('from_date', '<=', $validated['to_date'])
            ->where('to_date', '>=', $validated['from_date'])
            ->exists();

        if ($overlap) {
            return back()->with('error', 'You already have a leave request for this period.')->withInput();
        }

        Leave::create([
            'user_id'    => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'from_date'  => $validated['from_date'],
            'to_date'    => $validated['to_date'],
            'total_days' => max(1, $total),
            'reason'     => $validated['reason'],
            'status'     => 'pending',
        ]);

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Leave detail.
     */
    public function show(Leave $leave)
    {
        if ($leave->user_id !== Auth::id() && !Auth::user()->hasPermission('manage-leaves')) {
            abort(403);
        }

        $leave->load(['user', 'approver']);
        return view('leaves.show', compact('leave'));
    }

    /**
     * Cancel a pending leave.
     */
    public function cancel(Leave $leave)
    {
        if ($leave->user_id !== Auth::id()) {
            abort(403);
        }
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leaves can be cancelled.');
        }

        $leave->update(['status' => 'cancelled']);
        return redirect()->route('leaves.index')->with('success', 'Leave request cancelled.');
    }

    /**
     * Admin: All leave requests.
     */
    public function adminIndex(Request $request)
    {
        $query = Leave::with(['user', 'approver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $leaves     = $query->paginate(20)->appends($request->except('page'));
        $users      = User::orderBy('name')->get();
        $leaveTypes = Leave::$leaveTypes;

        return view('leaves.admin.index', compact('leaves', 'users', 'leaveTypes'));
    }

    /**
     * Admin: Approve a leave.
     */
    public function approve(Request $request, Leave $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leaves can be approved.');
        }

        $validated = $request->validate(['admin_notes' => 'nullable|string|max:500']);

        $leave->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        // Mark attendance as on_leave for the date range
        $this->markAttendanceForLeave($leave);

        return back()->with('success', 'Leave approved successfully.');
    }

    /**
     * Admin: Reject a leave.
     */
    public function reject(Request $request, Leave $leave)
    {
        if ($leave->status !== 'pending') {
            return back()->with('error', 'Only pending leaves can be rejected.');
        }

        $validated = $request->validate(['admin_notes' => 'nullable|string|max:500']);

        $leave->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        return back()->with('success', 'Leave rejected.');
    }

    /**
     * Mark attendance records as on_leave for approved leave range.
     */
    private function markAttendanceForLeave(Leave $leave): void
    {
        $current = $leave->from_date->copy();
        while ($current->lte($leave->to_date)) {
            if (!$current->isWeekend()) {
                Attendance::updateOrCreate(
                    ['user_id' => $leave->user_id, 'date' => $current->toDateString()],
                    ['status' => 'on_leave']
                );
            }
            $current->addDay();
        }
    }
}
