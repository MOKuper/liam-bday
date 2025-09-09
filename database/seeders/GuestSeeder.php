<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guests = [
            [
                'name' => 'Emma Thompson',
                'is_child' => true,
                'parent_name' => 'Lisa Thompson',
                'parent_email' => 'lisa@example.com',
                'parent_phone' => '555-0101',
            ],
            [
                'name' => 'Oliver Martinez',
                'is_child' => true,
                'parent_name' => 'Carlos Martinez',
                'parent_email' => 'carlos@example.com',
                'parent_phone' => '555-0102',
            ],
            [
                'name' => 'Sophia Chen',
                'is_child' => true,
                'parent_name' => 'Wei Chen',
                'parent_email' => 'wei@example.com',
                'parent_phone' => '555-0103',
            ],
            [
                'name' => 'Noah Williams',
                'is_child' => true,
                'parent_name' => 'Sarah Williams',
                'parent_email' => 'sarah@example.com',
                'parent_phone' => '555-0104',
            ],
        ];

        foreach ($guests as $guestData) {
            Guest::create($guestData);
        }
    }
}
