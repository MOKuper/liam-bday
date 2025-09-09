<?php

namespace Database\Seeders;

use App\Models\PartyDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PartyDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartyDetail::create([
            'child_name' => 'Liam',
            'child_age' => 5,
            'party_date' => Carbon::now()->addDays(30)->toDateString(),
            'start_time' => '11:45:00',
            'end_time' => '13:45:00',
            'venue_name' => 'VROG',
            'venue_address' => 'Mr. Visserplein 7 1011 RD Amsterdam',
            'venue_map_url' => 'https://maps.app.goo.gl/K7w6wexZSB7U5Y1BA',
            'parking_info' => '',
            'theme' => 'Trampolines & Freerunning',
            'activities' => '',
            'parent_contact_info' => '',
            'gift_suggestions' => 'Liam loves dinosaurs, LEGO, books, and art supplies!',
        ]);
    }
}

