<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin Role - Has all permissions
        $superAdmin = Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Administrator',
                'description' => 'Full access to all features and settings',
                'is_active' => true,
            ]
        );
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        // Administrator Role - Most permissions except sensitive operations
        $admin = Role::updateOrCreate(
            ['slug' => 'administrator'],
            [
                'name' => 'Administrator',
                'description' => 'Administrative access with most permissions',
                'is_active' => true,
            ]
        );
        $adminPermissions = Permission::whereNotIn('slug', [
            'delete-users',
            'delete-roles',
            'clear-system-logs',
            'optimize-application',
        ])->pluck('id');
        $admin->permissions()->sync($adminPermissions);

        // Manager Role - Can manage events, markets, and view reports
        $manager = Role::updateOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'Can manage events, markets, and view reports',
                'is_active' => true,
            ]
        );
        $managerPermissions = Permission::whereIn('group', [
            'Dashboard',
            'Events',
            'Markets',
            'Market Rates',
            'Risk',
            'Profile',
        ])->pluck('id');
        $manager->permissions()->sync($managerPermissions);

        // Editor Role - Can view and edit events/markets
        $editor = Role::updateOrCreate(
            ['slug' => 'editor'],
            [
                'name' => 'Editor',
                'description' => 'Can view and edit events and markets',
                'is_active' => true,
            ]
        );
        $editorPermissions = Permission::whereIn('slug', [
            'view-dashboard',
            'view-events',
            'view-all-events',
            'view-event-details',
            'edit-events',
            'view-markets',
            'view-all-markets',
            'view-market-details',
            'view-market-rates',
            'view-market-rate-details',
            'view-own-profile',
            'edit-own-profile',
            'change-own-password',
        ])->pluck('id');
        $editor->permissions()->sync($editorPermissions);

        // Viewer Role - Read-only access
        $viewer = Role::updateOrCreate(
            ['slug' => 'viewer'],
            [
                'name' => 'Viewer',
                'description' => 'Read-only access to view data',
                'is_active' => true,
            ]
        );
        $viewerPermissions = Permission::whereIn('slug', [
            'view-dashboard',
            'view-events',
            'view-all-events',
            'view-event-details',
            'view-markets',
            'view-all-markets',
            'view-market-details',
            'view-market-rates',
            'view-market-rate-details',
            'view-risk-markets',
            'view-own-profile',
            'edit-own-profile',
            'change-own-password',
        ])->pluck('id');
        $viewer->permissions()->sync($viewerPermissions);

        $this->command->info('Roles seeded successfully!');
        $this->command->info('Total roles created: ' . Role::count());
    }
}
