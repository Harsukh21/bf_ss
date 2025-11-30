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
            'delete-database-logs',
            'optimize-application',
        ])->pluck('id');
        $admin->permissions()->sync($adminPermissions);

        $this->command->info('Roles seeded successfully!');
        $this->command->info('Total roles created: ' . Role::count());
    }
}
