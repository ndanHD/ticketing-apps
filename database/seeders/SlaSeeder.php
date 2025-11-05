<?php

namespace Database\Seeders;

use App\Models\Sla;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class SlaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slas = [
            [
                'name' => 'Critical',
                'response_time' => 1, // 1 hour
                'resolution_time' => 4, // 4 hours
                'description' => 'Critical issues affecting multiple users'
            ],
            [
                'name' => 'High',
                'response_time' => 2, // 2 hours
                'resolution_time' => 8, // 8 hours
                'description' => 'High priority issues affecting individual users'
            ],
            [
                'name' => 'Medium',
                'response_time' => 4, // 4 hours
                'resolution_time' => 24, // 24 hours
                'description' => 'Medium priority issues with workarounds available'
            ],
            [
                'name' => 'Low',
                'response_time' => 8, // 8 hours
                'resolution_time' => 48, // 48 hours
                'description' => 'Low priority issues and feature requests'
            ]
        ];

        foreach ($slas as $sla) {
            Sla::create(array_merge(['id' => Str::uuid()], $sla));
        }
    }
}
