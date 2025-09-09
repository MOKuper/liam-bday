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
            'start_time' => '14:00:00',
            'end_time' => '17:00:00',
            'venue_name' => 'Fun Zone Play Center',
            'venue_address' => '123 Birthday Lane, Party City, PC 12345',
            'venue_map_url' => 'https://maps.google.com',
            'parking_info' => 'Free parking available in the venue lot. Overflow parking across the street.',
            'theme' => 'Dinosaur Adventure',
            'activities' => 'Bouncy castle, face painting, treasure hunt, and dino egg excavation!',
            'parent_contact_info' => 'Mom: Sarah (555) 123-4567 | Dad: John (555) 987-6543',
            'gift_suggestions' => 'Liam loves dinosaurs, LEGO, books, and art supplies!',
        ]);
    }
}
