<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Find admin role
        $adminRole = Role::where('slug', 'admin')->first();
        
        // Check if admin user already exists
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password') // Use a more secure password in production
            ]
        );
        
        // Attach admin role to user if not already attached
        if ($adminRole && !$admin->roles->contains($adminRole->id)) {
            $admin->roles()->attach($adminRole->id);
        }
    }
}