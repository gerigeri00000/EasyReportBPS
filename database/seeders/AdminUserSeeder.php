<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user (if doesn't exist)
        $user = User::firstOrCreate(
            ['email' => 'admin@bps.go.id'],
            [
                'name' => 'Admin Staff',
                'password' => Hash::make('password'), // Change this after first login!
                'email_verified_at' => now(),
            ]
        );

        if ($user->wasRecentlyCreated) {
            echo "Admin user created: admin@bps.go.id\n";
            echo "IMPORTANT: Please change the default password after first login.\n";
        } else {
            echo "Admin user already exists.\n";
        }
    }
}
