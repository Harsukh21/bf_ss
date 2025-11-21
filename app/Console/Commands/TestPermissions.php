<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class TestPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:test {user_id?} {--permission=} {--role=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test role and permission system - check cache, permissions, and roles for users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $permission = $this->option('permission');
        $role = $this->option('role');

        if (!$userId) {
            return $this->listAllUsers();
        }

        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("Testing Permissions for User: {$user->name} (ID: {$user->id}, Email: {$user->email})");
        $this->line(str_repeat('=', 60));

        // Check Cache
        $this->testCache($user);

        // Check Roles
        $this->testRoles($user, $role);

        // Check Permissions
        $this->testPermissions($user, $permission);

        return 0;
    }

    /**
     * List all users
     */
    private function listAllUsers()
    {
        $users = User::with('roles')->get();

        if ($users->isEmpty()) {
            $this->warn('No users found.');
            return 1;
        }

        $this->info('Available Users:');
        $this->line(str_repeat('-', 80));
        
        $headers = ['ID', 'Name', 'Email', 'Roles'];
        $rows = [];

        foreach ($users as $user) {
            $roleNames = $user->roles->pluck('name')->join(', ') ?: 'No roles';
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $roleNames,
            ];
        }

        $this->table($headers, $rows);
        $this->line('');
        $this->info('Usage: php artisan permissions:test {user_id} [--permission=permission-slug] [--role=role-slug]');
        $this->line('Example: php artisan permissions:test 1 --permission=view-notifications');

        return 0;
    }

    /**
     * Test cache for user
     */
    private function testCache(User $user)
    {
        $this->info('Cache Status:');
        $this->line('');

        $permissionsKey = $user->getPermissionsCacheKey();
        $rolesKey = $user->getRolesCacheKey();

        $permissionsCached = Cache::has($permissionsKey);
        $rolesCached = Cache::has($rolesKey);

        $this->line("  Permissions Cache Key: {$permissionsKey}");
        $this->line("  Status: " . ($permissionsCached ? '<fg=green>✓ Cached</>' : '<fg=red>✗ Not Cached</>'));
        
        $this->line("  Roles Cache Key: {$rolesKey}");
        $this->line("  Status: " . ($rolesCached ? '<fg=green>✓ Cached</>' : '<fg=red>✗ Not Cached</>'));

        if (!$permissionsCached || !$rolesCached) {
            $this->warn('  Cache not found. Loading cache now...');
            $user->loadPermissionsIntoCache();
            $user->loadRolesIntoCache();
            $this->info('  Cache loaded successfully.');
        }

        $this->line('');
    }

    /**
     * Test roles for user
     */
    private function testRoles(User $user, ?string $testRole = null)
    {
        $this->info('Roles:');
        $this->line('');

        $cachedRoles = $user->getCachedRoles();
        $dbRoles = $user->roles()->pluck('slug')->toArray();

        $this->line("  Cached Roles (" . count($cachedRoles) . "): " . implode(', ', $cachedRoles ?: ['None']));
        $this->line("  Database Roles (" . count($dbRoles) . "): " . implode(', ', $dbRoles ?: ['None']));

        if ($testRole) {
            $hasRole = $user->hasRole($testRole);
            $this->line('');
            $this->line("  Has Role '{$testRole}': " . ($hasRole ? '<fg=green>YES</>' : '<fg=red>NO</>'));
        }

        $this->line('');
    }

    /**
     * Test permissions for user
     */
    private function testPermissions(User $user, ?string $testPermission = null)
    {
        $this->info('Permissions:');
        $this->line('');

        $cachedPermissions = $user->getCachedPermissions();

        $this->line("  Total Cached Permissions: " . count($cachedPermissions));
        $this->line('');

        if (count($cachedPermissions) > 0) {
            $this->line("  Permission List:");
            foreach ($cachedPermissions as $perm) {
                $this->line("    - {$perm}");
            }
        } else {
            $this->warn('  No permissions found. User may not have any roles assigned or roles have no permissions.');
        }

        $this->line('');

        if ($testPermission) {
            $hasPermission = $user->hasPermission($testPermission);
            $this->line("  Has Permission '{$testPermission}': " . ($hasPermission ? '<fg=green>YES</>' : '<fg=red>NO</>'));
            $this->line('');
        }

        // Test super-admin
        if ($user->hasRole('super-admin')) {
            $this->info('  <fg=yellow>Super Admin detected - has ALL permissions</>');
            $this->line('');
        }
    }
}
