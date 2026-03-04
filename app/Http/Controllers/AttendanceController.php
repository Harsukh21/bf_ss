<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * My attendance list with today's widget.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        $records = Attendance::forUser($user->id)
            ->forMonth($year, $month)
            ->orderBy('date', 'desc')
            ->get();

        $today = Attendance::forUser($user->id)
            ->forDate(today()->toDateString())
            ->first();

        $monthlyStats = [
            'present'   => $records->where('status', 'present')->count(),
            'absent'    => $records->where('status', 'absent')->count(),
            'half_day'  => $records->where('status', 'half_day')->count(),
            'on_leave'  => $records->where('status', 'on_leave')->count(),
            'total_hours' => round($records->sum('total_hours'), 2),
        ];

        return view('attendance.index', compact('records', 'today', 'month', 'year', 'monthlyStats'));
    }

    /**
     * Clock in for today.
     */
    public function clockIn(Request $request)
    {
        $user  = Auth::user();
        $today = today()->toDateString();

        $attendance = Attendance::firstOrNew(['user_id' => $user->id, 'date' => $today]);

        if ($attendance->login_time) {
            return back()->with('error', 'You have already clocked in today.');
        }

        // Check if today is a holiday
        if (Holiday::isHoliday(today())) {
            return back()->with('error', 'Today is a holiday. No attendance needed.');
        }

        // Check if there's an approved leave for today
        $onLeave = Leave::forUser($user->id)
            ->approved()
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->exists();

        if ($onLeave) {
            return back()->with('error', 'You have an approved leave for today.');
        }

        $attendance->login_time = now()->format('H:i:s');
        $attendance->status     = 'incomplete';
        $attendance->save();

        return back()->with('success', 'Clocked in at ' . now()->format('h:i A'));
    }

    /**
     * Start break.
     */
    public function breakStart(Request $request)
    {
        $today      = today()->toDateString();
        $attendance = Attendance::forUser(Auth::id())->forDate($today)->first();

        if (!$attendance || !$attendance->login_time) {
            return back()->with('error', 'You must clock in first.');
        }
        if ($attendance->break_start_time) {
            return back()->with('error', 'Break has already started.');
        }
        if ($attendance->logout_time) {
            return back()->with('error', 'You have already clocked out.');
        }

        $attendance->break_start_time = now()->format('H:i:s');
        $attendance->save();

        return back()->with('success', 'Break started at ' . now()->format('h:i A'));
    }

    /**
     * End break.
     */
    public function breakEnd(Request $request)
    {
        $today      = today()->toDateString();
        $attendance = Attendance::forUser(Auth::id())->forDate($today)->first();

        if (!$attendance || !$attendance->break_start_time) {
            return back()->with('error', 'Break has not been started yet.');
        }
        if ($attendance->break_end_time) {
            return back()->with('error', 'Break has already ended.');
        }

        $attendance->break_end_time = now()->format('H:i:s');
        $attendance->save();
        $attendance->recalculateHours();

        return back()->with('success', 'Break ended at ' . now()->format('h:i A'));
    }

    /**
     * Clock out for today.
     */
    public function clockOut(Request $request)
    {
        $today      = today()->toDateString();
        $attendance = Attendance::forUser(Auth::id())->forDate($today)->first();

        if (!$attendance || !$attendance->login_time) {
            return back()->with('error', 'You must clock in first.');
        }
        if ($attendance->logout_time) {
            return back()->with('error', 'You have already clocked out today.');
        }

        $attendance->logout_time = now()->format('H:i:s');
        $attendance->save();
        $attendance->recalculateHours();

        return back()->with('success', 'Clocked out at ' . now()->format('h:i A') . '. Total: ' . $attendance->formatted_total_hours);
    }

    /**
     * Admin: All users attendance list.
     */
    public function adminIndex(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        $query = Attendance::with('user')
            ->forMonth($year, $month)
            ->orderBy('date', 'desc');

        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->paginate(20)->appends($request->except('page'));
        $users   = User::orderBy('name')->get();

        return view('attendance.admin.index', compact('records', 'users', 'month', 'year'));
    }

    /**
     * Admin: Single user attendance.
     */
    public function adminShow(Request $request, User $user)
    {
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        $records = Attendance::forUser($user->id)
            ->forMonth($year, $month)
            ->orderBy('date', 'desc')
            ->get();

        $monthlyStats = [
            'present'     => $records->where('status', 'present')->count(),
            'absent'      => $records->where('status', 'absent')->count(),
            'half_day'    => $records->where('status', 'half_day')->count(),
            'on_leave'    => $records->where('status', 'on_leave')->count(),
            'total_hours' => round($records->sum('total_hours'), 2),
        ];

        return view('attendance.admin.show', compact('user', 'records', 'month', 'year', 'monthlyStats'));
    }

    /**
     * Admin: Edit form for a single attendance record.
     */
    public function adminEdit(Attendance $attendance)
    {
        $attendance->load('user');
        return view('attendance.edit', compact('attendance'));
    }

    /**
     * Admin: Update an attendance record.
     */
    public function adminUpdate(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'login_time'       => 'nullable|date_format:H:i',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time'   => 'nullable|date_format:H:i|after:break_start_time',
            'logout_time'      => 'nullable|date_format:H:i|after:login_time',
            'status'           => 'required|in:present,absent,half_day,on_leave,holiday,incomplete',
            'notes'            => 'nullable|string|max:500',
        ]);

        // Append seconds for TIME columns
        foreach (['login_time', 'break_start_time', 'break_end_time', 'logout_time'] as $field) {
            if (!empty($validated[$field])) {
                $validated[$field] .= ':00';
            }
        }

        $attendance->update($validated);
        $attendance->recalculateHours();

        return redirect()->route('attendance.admin.index')->with('success', 'Attendance record updated.');
    }
}
