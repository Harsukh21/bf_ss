<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    /**
     * Seed the application's database.
     * 
     * IMPORTANT: Run seeders in this order:
     * 1. PermissionSeeder - Creates all permissions
     * 2. RoleSeeder - Creates roles and assigns permissions
     * 3. UserSeeder - Creates users and assigns roles
     * 4. Other seeders...
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,  // Must run first - creates permissions
            RoleSeeder::class,        // Must run second - creates roles and assigns permissions
            UserSeeder::class,        // Must run third - creates users and assigns roles
            EventSeeder::class,
            MarketListSeeder::class,
        ]);
    }
}
