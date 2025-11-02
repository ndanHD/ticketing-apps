<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static $roles = [];
    public function run(): void
    {
        //

        $roles = [
            'super_admin' => ['id' => Str::uuid(), 'can_handle_ticket' => true],
            'admin' => ['id' => Str::uuid(), 'can_handle_ticket' => true],
            'user_handler' => ['id' => Str::uuid(), 'can_handle_ticket' => true],
            'user' => ['id' => Str::uuid(), 'can_handle_ticket' => false],
        ];

        foreach ($roles as $name => $data) {
            $role = Role::create(array_merge(['name' => $name], $data));
            self::$roles[$name] = $role->id; // simpan UUID ke array static
        }
    }
}
