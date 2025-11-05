<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Bug Report',
                'description' => 'Report a software bug or technical issue',
                'is_active' => true
            ],
            [
                'name' => 'Feature Request',
                'description' => 'Request for new features or enhancements',
                'is_active' => true
            ],
            [
                'name' => 'Technical Support',
                'description' => 'Get help with technical problems',
                'is_active' => true
            ],
            [
                'name' => 'Account Issues',
                'description' => 'Problems with user accounts or access',
                'is_active' => true
            ]
        ];

        foreach ($types as $type) {
            TicketType::create(array_merge(['id' => Str::uuid()], $type));
        }
    }
}
