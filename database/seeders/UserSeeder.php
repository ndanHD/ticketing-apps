<?php

namespace Database\Seeders;

use App\Models\Outlet;
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

        $outlet = Outlet::firstOrCreate(
            ['name' => 'Outlet Pusat'],
            ['address' => 'Jl. Merdeka No.1']
        );

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['admin'],
            'outlet_id' => $outlet->id,
            'name' => 'Ngadmin',
            'job_tittle' => 'Lead Outlet',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['handler'],
            'outlet_id' => $outlet->id,
            'name' => 'Hendri',
            'job_tittle' => 'IT',
            'email' => 'handler@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $outlet->id,
            'name' => 'Sri',
            'job_tittle' => 'Kasir',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['superadmin'],
            'outlet_id' => $outlet->id,
            'name' => 'Supri',
            'job_tittle' => 'Manager',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
    }
}
