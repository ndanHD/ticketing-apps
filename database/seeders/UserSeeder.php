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

        // Create additional outlets and users per outlet
        $jakarta = Outlet::firstOrCreate(['name' => 'Jakarta'], ['address' => 'Jakarta Center']);
        $surabaya = Outlet::firstOrCreate(['name' => 'Surabaya'], ['address' => 'Surabaya Center']);
        $bandung = Outlet::firstOrCreate(['name' => 'Bandung'], ['address' => 'Bandung Center']);

        // Jakarta users
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['admin'],
            'outlet_id' => $jakarta->id,
            'name' => 'Admin Jakarta',
            'job_tittle' => 'Admin',
            'email' => 'admin.jakarta@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['handler'],
            'outlet_id' => $jakarta->id,
            'name' => 'Handler Jakarta',
            'job_tittle' => 'Handler',
            'email' => 'handler.jakarta@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        // Jakarta 'user' accounts
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $jakarta->id,
            'name' => 'User Jakarta 1',
            'job_tittle' => 'Kasir',
            'email' => 'user.jakarta.1@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $jakarta->id,
            'name' => 'User Jakarta 2',
            'job_tittle' => 'Kasir',
            'email' => 'user.jakarta.2@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $jakarta->id,
            'name' => 'User Jakarta 3',
            'job_tittle' => 'Kasir',
            'email' => 'user.jakarta.3@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        // Surabaya users
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['admin'],
            'outlet_id' => $surabaya->id,
            'name' => 'Admin Surabaya',
            'job_tittle' => 'Admin',
            'email' => 'admin.surabaya@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['handler'],
            'outlet_id' => $surabaya->id,
            'name' => 'Handler Surabaya',
            'job_tittle' => 'Handler',
            'email' => 'handler.surabaya@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        // Surabaya 'user' accounts
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $surabaya->id,
            'name' => 'User Surabaya 1',
            'job_tittle' => 'Kasir',
            'email' => 'user.surabaya.1@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $surabaya->id,
            'name' => 'User Surabaya 2',
            'job_tittle' => 'Kasir',
            'email' => 'user.surabaya.2@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $surabaya->id,
            'name' => 'User Surabaya 3',
            'job_tittle' => 'Kasir',
            'email' => 'user.surabaya.3@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);

        // Bandung users
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['admin'],
            'outlet_id' => $bandung->id,
            'name' => 'Admin Bandung',
            'job_tittle' => 'Admin',
            'email' => 'admin.bandung@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['handler'],
            'outlet_id' => $bandung->id,
            'name' => 'Handler Bandung',
            'job_tittle' => 'Handler',
            'email' => 'handler.bandung@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        // Bandung 'user' accounts
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $bandung->id,
            'name' => 'User Bandung 1',
            'job_tittle' => 'Kasir',
            'email' => 'user.bandung.1@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $bandung->id,
            'name' => 'User Bandung 2',
            'job_tittle' => 'Kasir',
            'email' => 'user.bandung.2@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
        Users::create([
            'id' => Str::uuid(),
            'role_id' => $roles['user'],
            'outlet_id' => $bandung->id,
            'name' => 'User Bandung 3',
            'job_tittle' => 'Kasir',
            'email' => 'user.bandung.3@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true
        ]);
    }
}
