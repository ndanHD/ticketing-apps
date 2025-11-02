<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Users;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = RoleSeeder::$roles;

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['super_admin'],
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['admin'],
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user_handler'],
            'name' => 'Handler User',
            'email' => 'handler@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
    }
}
