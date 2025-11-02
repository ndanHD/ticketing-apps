<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Users;

class UserSeeder extends Seeder
{
    public function run()
    {
        $superadminRole = Role::where('name', 'Superadmin')->first();

        Users::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'role_id' => $superadminRole->id,
                'password' => 'superadmin123',
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        $this->command->info('Superadmin created!');
    }
}
