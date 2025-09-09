<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PartyDetail extends Model
{
    protected $fillable = [
        'child_name',
        'child_age',
        'party_date',
        'start_time',
        'end_time',
        'venue_name',
        'venue_address',
        'venue_map_url',
        'parking_info',
        'theme',
        'activities',
        'parent_contact_info',
        'gift_suggestions',
    ];

    protected $casts = [
        'party_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'child_age' => 'integer',
    ];

    public function getDaysUntilParty()
    {
        return Carbon::now()->diffInDays($this->party_date, false);
    }

    public function isPartyToday()
    {
        return $this->party_date->isToday();
    }

    public function isPartyPast()
    {
        return $this->party_date->isPast();
    }

    public function getFormattedDate()
    {
        return $this->party_date
            ->locale('nl')
            ->translatedFormat('l j F Y');
    }

    public function getFormattedTime()
    {
        return Carbon::parse($this->start_time)
                   ->format('H:i') . ' - ' . Carbon::parse($this->end_time)
                   ->format('H:i');
    }
}
