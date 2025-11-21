<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
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
        $query = User::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('telegram_id', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->get('status') === 'inactive') {
                $query->whereNull('email_verified_at');
            }
        }

        // Apply Telegram verification filter
        if ($request->filled('telegram_verified')) {
            if ($request->get('telegram_verified') === 'verified') {
                $query->whereNotNull('telegram_chat_id');
            } elseif ($request->get('telegram_verified') === 'unverified') {
                $query->whereNotNull('telegram_id')->whereNull('telegram_chat_id');
            } elseif ($request->get('telegram_verified') === 'not_set') {
                $query->whereNull('telegram_id');
            }
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $users = $query->with('roles')->latest()->paginate(10);
        
        $roles = Role::where('is_active', true)->get();
        
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('users.index')
                ->with('error', 'You are not authorized to create users.');
        }
        $roles = Role::where('is_active', true)->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('users.index')
                ->with('error', 'You are not authorized to create users.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'web_pin' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:6'],
            'telegram_id' => ['nullable', 'string', 'max:100'],
        ], [
            'web_pin.regex' => 'Web Pin must contain only numbers.',
            'web_pin.min' => 'Web Pin must be at least 6 digits.',
            'telegram_id.max' => 'Telegram ID must not exceed 100 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'web_pin' => $request->web_pin,
            'telegram_id' => $request->telegram_id,
        ]);

        // Assign roles if provided
        if ($request->filled('roles')) {
            $user->assignRoles($request->roles);
            // Clear permission cache after role assignment
            $user->clearPermissionCache();
            // Reload cache with new permissions
            $user->loadPermissionsIntoCache();
            $user->loadRolesIntoCache();
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles.permissions');
        $roles = Role::where('is_active', true)->with('permissions')->get();
        $permissions = Permission::all()->groupBy('group');
        return view('users.show', compact('user', 'roles', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('users.index')
                ->with('error', 'You are not authorized to edit users.');
        }
        $user->load('roles');
        $roles = Role::where('is_active', true)->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('users.index')
                ->with('error', 'You are not authorized to update users.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'web_pin' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:6'],
            'telegram_id' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:10'],
        ], [
            'web_pin.regex' => 'Web Pin must contain only numbers.',
            'web_pin.min' => 'Web Pin must be at least 6 digits.',
            'telegram_id.max' => 'Telegram ID must not exceed 100 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'web_pin' => $request->web_pin,
            'telegram_id' => $request->telegram_id,
            'date_of_birth' => $request->date_of_birth,
            'bio' => $request->bio,
            'timezone' => $request->timezone,
            'language' => $request->language,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Handle status update
        if ($request->has('email_verified_at_status')) {
            if ($request->email_verified_at_status === 'active') {
                $updateData['email_verified_at'] = $user->email_verified_at ?? now();
            } else {
                $updateData['email_verified_at'] = null;
            }
        }

        $user->update($updateData);

        // Update roles if provided
        if ($request->has('roles')) {
            $user->assignRoles($request->roles ?? []);
            // Clear permission cache after role update
            $user->clearPermissionCache();
            // Reload cache with new permissions
            $user->loadPermissionsIntoCache();
            $user->loadRolesIntoCache();
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('users.index')
                ->with('error', 'You are not authorized to delete users.');
        }

        // Prevent deletion of the current user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Prevent deletion of protected users
        $protectedEmails = ['harsukh21@gmail.com', 'sam.parkinson7777@gmail.com'];
        if (in_array($user->email, $protectedEmails)) {
            return redirect()->route('users.index')
                ->with('error', 'This user cannot be deleted.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User '{$userName}' has been deleted successfully.");
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, User $user)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('users.index')
                ->with('error', 'You are not authorized to update user status.');
        }

        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->status === 'active') {
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }
        } else {
            if ($user->email_verified_at) {
                $user->email_verified_at = null;
                $user->save();
            }
        }

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' status updated successfully.");
    }

    /**
     * Search users (redirects to index with search parameter)
     */
    public function search(Request $request)
    {
        return redirect()->route('users.index', $request->all());
    }

    /**
     * Update user roles
     */
    public function updateRoles(Request $request, User $user)
    {
        if (!$this->isAuthorized()) {
            return redirect()->route('users.index')
                ->with('error', 'You are not authorized to update user roles.');
        }

        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->assignRoles($request->roles ?? []);
        
        // Clear permission cache after role update
        $user->clearPermissionCache();
        
        // Reload cache with new permissions
        $user->loadPermissionsIntoCache();
        $user->loadRolesIntoCache();

        return redirect()->back()
            ->with('success', "User '{$user->name}' roles updated successfully.");
    }
}
