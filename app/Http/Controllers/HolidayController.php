<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    /**
     * View holiday calendar.
     */
    public function index(Request $request)
    {
        $year     = $request->integer('year', now()->year);
        $holidays = Holiday::getForYear($year)->keyBy(fn($h) => $h->date->format('Y-m-d'));
        $years    = range(now()->year - 1, now()->year + 2);

        return view('holidays.index', compact('holidays', 'year', 'years'));
    }

    /**
     * Add holiday form.
     */
    public function create()
    {
        return view('holidays.create');
    }

    /**
     * Save holiday.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'date'         => 'required|date|unique:holidays,date',
            'description'  => 'nullable|string|max:500',
            'is_recurring' => 'boolean',
        ]);

        Holiday::create([
            ...$validated,
            'is_recurring' => $request->boolean('is_recurring'),
            'created_by'   => Auth::id(),
        ]);

        return redirect()->route('holidays.index')->with('success', 'Holiday added successfully.');
    }

    /**
     * Edit holiday form.
     */
    public function edit(Holiday $holiday)
    {
        return view('holidays.edit', compact('holiday'));
    }

    /**
     * Update holiday.
     */
    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'date'         => 'required|date|unique:holidays,date,' . $holiday->id,
            'description'  => 'nullable|string|max:500',
            'is_recurring' => 'boolean',
        ]);

        $holiday->update([
            ...$validated,
            'is_recurring' => $request->boolean('is_recurring'),
        ]);

        return redirect()->route('holidays.index')->with('success', 'Holiday updated successfully.');
    }

    /**
     * Delete holiday.
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holidays.index')->with('success', 'Holiday deleted.');
    }
}
