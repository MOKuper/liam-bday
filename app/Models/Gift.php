<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Gift extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'price_range_min',
        'price_range_max',
        'store_suggestion',
        'image_url',
        'priority',
        'is_claimed',
        'claimed_by_name',
        'claimed_by_email',
        'claimed_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'price_range_min' => 'decimal:2',
        'price_range_max' => 'decimal:2',
        'priority' => 'integer',
        'is_claimed' => 'boolean',
        'is_active' => 'boolean',
        'claimed_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_claimed', false)->where('is_active', true);
    }

    public function scopeClaimed($query)
    {
        return $query->where('is_claimed', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Mutators & Accessors
    public function getPriceRangeTextAttribute()
    {
        if ($this->price_range_min && $this->price_range_max) {
            return "€{$this->price_range_min} - €{$this->price_range_max}";
        } elseif ($this->price_range_min) {
            return "€{$this->price_range_min}+";
        } elseif ($this->price_range_max) {
            return "Up to €{$this->price_range_max}";
        }
        return null;
    }

    public function getPriorityTextAttribute()
    {
        return match($this->priority) {
            1 => 'High',
            2 => 'Medium',
            3 => 'Low',
            default => 'Medium'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            1 => 'text-red-600 bg-red-100',
            2 => 'text-yellow-600 bg-yellow-100',
            3 => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    // Methods
    public function claimGift($name, $email)
    {
        $this->update([
            'is_claimed' => true,
            'claimed_by_name' => $name,
            'claimed_by_email' => $email,
            'claimed_at' => now(),
        ]);
    }

    public function unclaimGift()
    {
        $this->update([
            'is_claimed' => false,
            'claimed_by_name' => null,
            'claimed_by_email' => null,
            'claimed_at' => null,
        ]);
    }
}
