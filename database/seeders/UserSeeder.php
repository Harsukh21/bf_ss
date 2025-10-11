<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the default admin user
        User::updateOrCreate(
            ['email' => 'harsukh21@gmail.com'],
            [
                'name' => 'Harsukh',
                'email' => 'harsukh21@gmail.com',
                'password' => Hash::make('Har#$785'),
                'email_verified_at' => now(),
            ]
        );

        // Create additional test users (optional)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('harsukh21@gmail.com / Har#$785');
        $this->command->info('admin@example.com / password123');
        $this->command->info('user@example.com / password123');
    }
}