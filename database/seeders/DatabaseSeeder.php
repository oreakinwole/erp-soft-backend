<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AllowanceTypeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user for production deployment
        User::firstOrCreate(
            ['email' => 'admin@erp-soft.com'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@erp-soft.com',
                'password' => bcrypt('Admin@2025!'),
                'email_verified_at' => now(),
            ]
        );

        // Seed initial allowance types from config for DB-driven setup
        $this->call(AllowanceTypeSeeder::class);
    }
}
