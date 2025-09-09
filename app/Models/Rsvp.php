<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rsvp extends Model
{
    protected $fillable = [
        'guest_id',
        'status',
        'adults_attending',
        'children_attending',
        'dietary_restrictions',
        'special_needs',
        'notes',
        'responded_at',
    ];

    protected $casts = [
        'adults_attending' => 'integer',
        'children_attending' => 'integer',
        'responded_at' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function getTotalAttendees()
    {
        return $this->adults_attending + $this->children_attending;
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isDeclined()
    {
        return $this->status === 'declined';
    }
}
