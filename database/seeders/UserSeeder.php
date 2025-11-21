<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the default admin user
        $user = User::updateOrCreate(
            ['email' => 'harsukh21@gmail.com'],
            [
                'name' => 'Harsukh',
                'email' => 'harsukh21@gmail.com',
                'password' => Hash::make('Har#$785'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Super Admin role to the default user
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $user->assignRoles([$superAdminRole->id]);
            // Clear and reload cache
            $user->clearPermissionCache();
            $user->loadPermissionsIntoCache();
            $user->loadRolesIntoCache();
            $this->command->info('✅ Super Admin role assigned to harsukh21@gmail.com');
        } else {
            $this->command->warn('⚠️  Super Admin role not found. Please run RoleSeeder first!');
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('harsukh21@gmail.com / Har#$785');
        $this->command->info('admin@example.com / password123');
        $this->command->info('user@example.com / password123');
    }
}