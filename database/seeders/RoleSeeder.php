<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Role::firstOrCreate(
            ['name' => 'Superadmin'],
            ['can_handle_ticket' => true]
        );
        Role::firstOrCreate(
            ['name' => 'User'],
            ['can_handle_ticket' => false]
        );
        $this->command->info('Roles created: Superadmin & User');
    }
}
