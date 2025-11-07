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
            'role_id' => $roles['admin'],
            'name' => 'Ngadmin',
            'jabatan' => 'Lead Outlet',
            'outlet' => 'Jakarta',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['handler'],
            'name' => 'Hendri',
            'jabatan' => 'IT',
            'outlet' => 'Jakarta',
            'email' => 'handler@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'name' => 'Sri',
            'jabatan' => 'Kasir',
            'outlet' => 'Jakarta',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['superadmin'],
            'name' => 'Supri',
            'jabatan' => 'Manager',
            'outlet' => 'Jakarta',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
    }
}
