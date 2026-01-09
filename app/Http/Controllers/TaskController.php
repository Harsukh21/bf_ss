<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display all tasks
     */
    public function index(Request $request)
    {
        $query = Task::with(['creator', 'assignedUser']);

        // Role-based access control
        // Super Administrator can see all tasks
        // Other users can only see tasks assigned to them or created by them
        if (!Auth::user()->hasRole('super-admin')) {
            $query->where(function($q) {
                $q->where('assigned_to', Auth::id())
                  ->orWhere('created_by', Auth::id());
            });
        }

        $query->latest();

        $this->applyTaskFilters($request, $query);

        $tasks = $query->paginate(15)->appends($request->except('page'));
        $users = User::orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'users'));
    }

    /**
     * Show the form for creating a new task
     */
    public function create()
    {
        // Only Super Administrator can create tasks
        if (!Auth::user()->hasRole('super-admin')) {
            return redirect()->route('tasks.index')->with('error', 'Only Super Administrators can create tasks.');
        }

        $users = User::orderBy('name')->get();
        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created task
     */
    public function store(Request $request)
    {
        // Only Super Administrator can create tasks
        if (!Auth::user()->hasRole('super-admin')) {
            return redirect()->route('tasks.index')->with('error', 'Only Super Administrators can create tasks.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['progress'] = 0;

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        $task->load(['creator', 'assignedUser']);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task
     */
    public function edit(Task $task)
    {
        // Only Super Administrator or task creator can edit
        if (!Auth::user()->hasRole('super-admin') && $task->created_by !== Auth::id()) {
            return redirect()->route('tasks.show', $task)->with('error', 'You do not have permission to edit this task.');
        }

        $users = User::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'users'));
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, Task $task)
    {
        // Only Super Administrator or task creator can update
        if (!Auth::user()->hasRole('super-admin') && $task->created_by !== Auth::id()) {
            return redirect()->route('tasks.show', $task)->with('error', 'You do not have permission to update this task.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'progress' => 'required|integer|min:0|max:100',
            'due_date' => 'nullable|date',
        ]);

        // If status is completed, set completed_at
        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
            $validated['progress'] = 100;
        }

        // If status changed from completed to something else, clear completed_at
        if ($validated['status'] !== 'completed' && $task->status === 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully!');
    }

    /**
     * Remove the specified task
     */
    public function destroy(Task $task)
    {
        // Only Super Administrator or task creator can delete
        if (!Auth::user()->hasRole('super-admin') && $task->created_by !== Auth::id()) {
            return redirect()->route('tasks.show', $task)->with('error', 'You do not have permission to delete this task.');
        }

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }

    /**
     * Display tasks in progress
     */
    public function inProgress(Request $request)
    {
        $query = Task::with(['creator', 'assignedUser'])
            ->where('status', 'in_progress');

        // Role-based access control
        if (!Auth::user()->hasRole('super-admin')) {
            $query->where(function($q) {
                $q->where('assigned_to', Auth::id())
                  ->orWhere('created_by', Auth::id());
            });
        }

        $this->applyTaskFilters($request, $query, false);

        $tasks = $query->latest()->paginate(15)->appends($request->except('page'));
        $users = User::orderBy('name')->get();

        return view('tasks.in-progress', compact('tasks', 'users'));
    }

    /**
     * Display completed tasks
     */
    public function complete(Request $request)
    {
        $query = Task::with(['creator', 'assignedUser'])
            ->where('status', 'completed');

        // Role-based access control
        if (!Auth::user()->hasRole('super-admin')) {
            $query->where(function($q) {
                $q->where('assigned_to', Auth::id())
                  ->orWhere('created_by', Auth::id());
            });
        }

        $this->applyTaskFilters($request, $query, false);

        $tasks = $query->latest('completed_at')->paginate(15)->appends($request->except('page'));
        $users = User::orderBy('name')->get();

        return view('tasks.complete', compact('tasks', 'users'));
    }

    /**
     * Display due tasks
     */
    public function due(Request $request)
    {
        $query = Task::with(['creator', 'assignedUser'])
            ->due();

        // Role-based access control
        if (!Auth::user()->hasRole('super-admin')) {
            $query->where(function($q) {
                $q->where('assigned_to', Auth::id())
                  ->orWhere('created_by', Auth::id());
            });
        }

        $this->applyTaskFilters($request, $query);

        $tasks = $query->orderBy('due_date', 'asc')->paginate(15)->appends($request->except('page'));
        $users = User::orderBy('name')->get();

        return view('tasks.due', compact('tasks', 'users'));
    }

    private function applyTaskFilters(Request $request, $query, bool $allowStatus = true): void
    {
        // Filter by status
        if ($allowStatus && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by created by
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // Filter by progress range
        if ($request->filled('progress_min')) {
            $query->where('progress', '>=', $request->progress_min);
        }
        if ($request->filled('progress_max')) {
            $query->where('progress', '<=', $request->progress_max);
        }

        // Filter by due date range
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }
        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        // Filter overdue tasks
        if ($request->boolean('overdue')) {
            $query->overdue();
        }
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, Task $task)
    {
        // Only assigned user, creator, or Super Administrator can update status
        if ($task->assigned_to !== Auth::id() &&
            $task->created_by !== Auth::id() &&
            !Auth::user()->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this task status.'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        if ($validated['status'] === 'completed') {
            $task->markAsCompleted();
        } else {
            $task->status = $validated['status'];
            if ($validated['status'] !== 'completed') {
                $task->completed_at = null;
            }
            $task->save();
        }

        return response()->json(['success' => true, 'message' => 'Task status updated successfully!']);
    }

    /**
     * Update task progress
     */
    public function updateProgress(Request $request, Task $task)
    {
        // Only assigned user, creator, or Super Administrator can update progress
        if ($task->assigned_to !== Auth::id() &&
            $task->created_by !== Auth::id() &&
            !Auth::user()->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this task progress.'
            ], 403);
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $task->progress = $validated['progress'];

        // Auto-complete if progress is 100%
        if ($validated['progress'] == 100 && $task->status !== 'completed') {
            $task->status = 'completed';
            $task->completed_at = now();
        }

        $task->save();

        return response()->json(['success' => true, 'message' => 'Task progress updated successfully!']);
    }
}
