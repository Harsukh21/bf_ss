<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Check if current user is authorized to perform admin operations
     */
    private function isAuthorized()
    {
        $authorizedEmails = ['harsukh21@gmail.com', 'sam.parkinson7777@gmail.com'];
        return in_array(auth()->user()->email, $authorizedEmails);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not authorized to view roles.');
        }

        $query = Role::query()->withCount('users');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->get('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $roles = $query->with('permissions')->latest()->paginate(15);
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('roles.index')
                ->with('error', 'You are not authorized to create roles.');
        }

        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('roles.index')
                ->with('error', 'You are not authorized to create roles.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?: Str::slug($request->name);

        // Check if slug is unique
        if (Role::where('slug', $slug)->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['slug' => 'This slug is already taken.']);
        }

        $role = Role::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        // Assign permissions if provided
        if ($request->filled('permissions')) {
            $role->permissions()->sync($request->permissions);
            // Clear cache for all users with this role (if any)
            $role->load('users');
            $users = $role->users;
            foreach ($users as $user) {
                $user->clearPermissionCache();
                $user->loadPermissionsIntoCache();
                $user->loadRolesIntoCache();
            }
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('roles.index')
                ->with('error', 'You are not authorized to view roles.');
        }

        $role->load(['permissions', 'users']);
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        
        return view('roles.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('roles.index')
                ->with('error', 'You are not authorized to edit roles.');
        }

        $role->load('permissions');
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('roles.index')
                ->with('error', 'You are not authorized to update roles.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'slug' => ['nullable', 'string', 'max:255', 'unique:roles,slug,' . $role->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?: Str::slug($request->name);

        // Check if slug is unique (excluding current role)
        if (Role::where('slug', $slug)->where('id', '!=', $role->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['slug' => 'This slug is already taken.']);
        }

        $role->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : $role->is_active,
        ]);

        // Update permissions
        $role->permissions()->sync($request->permissions ?? []);
        
        // Clear cache for all users with this role
        $role->load('users');
        $users = $role->users;
        foreach ($users as $user) {
            $user->clearPermissionCache();
            $user->loadPermissionsIntoCache();
            $user->loadRolesIntoCache();
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('roles.index')
                ->with('error', 'You are not authorized to delete roles.');
        }

        $roleName = $role->name;
        
        // Get users with this role before deletion
        $users = $role->users;
        
        // Check if role has users assigned
        if ($users->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', "Cannot delete role '{$roleName}' because it has users assigned. Please remove users from this role first.");
        }

        $role->delete();
        
        // Note: Since we check that role has no users, no cache clearing needed
        // But if we ever allow deletion with users, we'd clear cache here

        return redirect()->route('roles.index')
            ->with('success', "Role '{$roleName}' has been deleted successfully.");
    }
}
