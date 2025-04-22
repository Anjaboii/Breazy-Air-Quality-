<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // First, check if the table already has data
        if (Role::count() > 0) {
            // Truncate the table to remove all existing records
            // Note: This will fail if there are foreign key constraints
            DB::statement('DELETE FROM roles');
            // Reset the auto-increment ID
            DB::statement('DELETE FROM sqlite_sequence WHERE name="roles"');
        }

        // Now create the roles
        Role::create([
            'name' => 'Administrator',
            'slug' => 'admin'
        ]);
        
        // Add other roles here
        Role::create([
            'name' => 'User',
            'slug' => 'user'
        ]);
    }
}