<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user (into admins table)
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_super_admin' => true,
            'permissions' => ['*']
        ]);

        // Regular user (into users table)
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        \App\Models\Place::create(['name' => 'Central Cafe', 'type' => 'cafe', 'location' => 'Downtown', 'current_crowd_count' => 12]);
        \App\Models\Place::create(['name' => 'City Gym', 'type' => 'gym', 'location' => 'Uptown', 'current_crowd_count' => 45]);
        \App\Models\Place::create(['name' => 'Public Library', 'type' => 'library', 'location' => 'Westside', 'current_crowd_count' => 5]);
        \App\Models\Place::create(['name' => 'Grand Mall', 'type' => 'mall', 'location' => 'Eastside', 'current_crowd_count' => 150]);
    }
}
