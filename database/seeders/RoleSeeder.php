<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class RoleSeeder extends Seeder
{
    /**
     * Menjalankan seeder role dan menyimpan UUID role ke properti static
     */
    public static $roles = [];
    public function run(): void
    {
        //

        $roles = [
            'superadmin' => ['id' => Str::uuid()],
            'admin' => ['id' => Str::uuid()],
            'handler' => ['id' => Str::uuid()],
            'user' => ['id' => Str::uuid()],
        ];

        foreach ($roles as $name => $data) {
            $role = Role::create(array_merge(['name' => $name], $data));
            self::$roles[$name] = $role->id; // simpan UUID ke array static
        }
    }
}
