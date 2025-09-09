<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'guest_id',
        'message',
        'drawing_path',
        'photo_path',
        'audio_path',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function hasMedia()
    {
        return $this->drawing_path || $this->photo_path || $this->audio_path;
    }
}
