<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee')
            ->orderByDesc('date')
            ->orderBy('employee_id');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->paginate(20)->appends($request->except('page'));
        $employees   = Employee::where('status', 'active')->orderBy('name')->get();

        return view('attendances.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'date'             => 'required|date',
            'start_time'       => 'nullable|date_format:H:i',
            'end_time'         => 'nullable|date_format:H:i|after:start_time',
            'start_break_time' => 'nullable|date_format:H:i',
            'end_break_time'   => 'nullable|date_format:H:i|after:start_break_time',
            'status'           => 'required|in:present,absent,half_day,late,on_leave,holiday',
            'note'             => 'nullable|string|max:1000',
        ], [
            'employee_id.unique' => 'An attendance record for this employee on this date already exists.',
        ]);

        // Unique check
        $exists = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'date' => 'An attendance record for this employee on this date already exists.',
            ]);
        }

        $validated['working_minutes'] = Attendance::calculateWorkingMinutes(
            $validated['start_time'] ?? null,
            $validated['end_time'] ?? null,
            $validated['start_break_time'] ?? null,
            $validated['end_break_time'] ?? null,
        );

        $validated['created_by'] = Auth::id();

        Attendance::create($validated);

        return redirect()->route('emp-attendance.index')
            ->with('success', 'Attendance record added successfully!');
    }

    public function show(Attendance $empAttendance)
    {
        $empAttendance->load('employee', 'creator');
        return view('attendances.show', ['attendance' => $empAttendance]);
    }

    public function edit(Attendance $empAttendance)
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('attendances.edit', [
            'attendance' => $empAttendance,
            'employees'  => $employees,
        ]);
    }

    public function update(Request $request, Attendance $empAttendance)
    {
        $validated = $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'date'             => 'required|date',
            'start_time'       => 'nullable|date_format:H:i',
            'end_time'         => 'nullable|date_format:H:i|after:start_time',
            'start_break_time' => 'nullable|date_format:H:i',
            'end_break_time'   => 'nullable|date_format:H:i|after:start_break_time',
            'status'           => 'required|in:present,absent,half_day,late,on_leave,holiday',
            'note'             => 'nullable|string|max:1000',
        ]);

        // Unique check (excluding current record)
        $exists = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->where('id', '!=', $empAttendance->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'date' => 'An attendance record for this employee on this date already exists.',
            ]);
        }

        $validated['working_minutes'] = Attendance::calculateWorkingMinutes(
            $validated['start_time'] ?? null,
            $validated['end_time'] ?? null,
            $validated['start_break_time'] ?? null,
            $validated['end_break_time'] ?? null,
        );

        $empAttendance->update($validated);

        return redirect()->route('emp-attendance.show', $empAttendance)
            ->with('success', 'Attendance record updated successfully!');
    }

    public function destroy(Attendance $empAttendance)
    {
        $empAttendance->delete();
        return redirect()->route('emp-attendance.index')
            ->with('success', 'Attendance record deleted successfully!');
    }
}
