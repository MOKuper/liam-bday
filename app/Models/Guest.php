<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'slug',
        'unique_url',
        'is_child',
        'parent_name',
        'parent_email',
        'parent_phone',
        'parent_name_nl',
        'parent_email_nl',
        'parent_phone_nl',
        'preferred_language',
        'expected_attendees',
        'qr_code_path',
        'friendship_photo_path',
    ];

    protected $casts = [
        'is_child' => 'boolean',
        'expected_attendees' => 'integer',
        'date_of_birth' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($guest) {
            $guest->slug = Str::slug($guest->name);
            $guest->unique_url = Str::random(16);
        });
    }

    public function rsvp()
    {
        return $this->hasOne(Rsvp::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getPersonalizedUrl()
    {
        return route('guest.landing', $this->unique_url);
    }

    public function generateQrCode()
    {
        if ($this->qr_code_path && Storage::disk('public')->exists($this->qr_code_path)) {
            return $this;
        }

        $invitationUrl = $this->getPersonalizedUrl();
        
        // Create QR code using Builder
        $result = (new Builder(
            writer: new PngWriter(),
            data: $invitationUrl,
            size: 300,
            margin: 10,
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            encoding: new Encoding('UTF-8')
        ))->build();

        // Create QR codes directory if it doesn't exist
        $qrCodePath = 'qr-codes/' . $this->unique_url . '.png';
        Storage::disk('public')->put($qrCodePath, $result->getString());
        
        $this->update(['qr_code_path' => $qrCodePath]);
        
        return $this;
    }

    public function getQrCodeUrl()
    {
        if (!$this->qr_code_path) {
            $this->generateQrCode();
        }
        
        return Storage::url($this->qr_code_path);
    }

    public function getAge()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getFormattedDateOfBirth()
    {
        return $this->date_of_birth ? $this->date_of_birth->format('Y-m-d') : null;
    }
}
