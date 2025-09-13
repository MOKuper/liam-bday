<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\PartyDetail;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class InvitationController extends Controller
{
    public function generateInvitation(Guest $guest, $theme = 'birthday')
    {
        $partyDetails = PartyDetail::first();
        
        // Create image canvas
        $manager = new ImageManager(new Driver());
        $canvas = $manager->create(800, 1200)->fill('#ffffff');
        
        // Load theme configuration
        $themeConfig = $this->getThemeConfig($theme);
        
        // Apply background
        $canvas->fill($themeConfig['background']);
        
        // Add decorative elements
        $this->addDecorations($canvas, $themeConfig);
        
        // Add main title
        $canvas->text("You're Invited!", 400, 150, function ($font) use ($themeConfig) {
            $font->file(resource_path('fonts/fun-font.ttf'));
            $font->size(48);
            $font->color($themeConfig['title_color']);
            $font->align('center');
        });
        
        // Add party title
        $canvas->text($partyDetails->child_name . "'s " . $partyDetails->child_age . "th Birthday", 400, 220, function ($font) use ($themeConfig) {
            $font->file(resource_path('fonts/fun-font.ttf'));
            $font->size(36);
            $font->color($themeConfig['text_color']);
            $font->align('center');
        });
        
        // Add hero image if available
        if ($guest->friendship_photo_path) {
            $heroImage = $manager->read(Storage::disk('public')->path($guest->friendship_photo_path));
            $heroImage->resize(300, 300);
            $canvas->place($heroImage, 'center', 250, 320);
        }
        
        // Add party details
        $details = [
            "📅 " . $partyDetails->getFormattedDate(),
            "🕐 " . $partyDetails->getFormattedTime(),  
            "📍 " . $partyDetails->venue_name,
        ];
        
        $yPosition = $guest->friendship_photo_path ? 650 : 400;
        foreach ($details as $detail) {
            $canvas->text($detail, 400, $yPosition, function ($font) use ($themeConfig) {
                $font->file(resource_path('fonts/regular.ttf'));
                $font->size(28);
                $font->color($themeConfig['text_color']);
                $font->align('center');
            });
            $yPosition += 50;
        }
        
        // Add RSVP URL
        $rsvpUrl = url('/invite/' . $guest->unique_url);
        $canvas->text("RSVP: " . $rsvpUrl, 400, $yPosition + 50, function ($font) use ($themeConfig) {
            $font->file(resource_path('fonts/regular.ttf'));
            $font->size(20);
            $font->color($themeConfig['url_color']);
            $font->align('center');
        });
        
        // Add QR code
        if ($guest->qr_code_path) {
            $qrImage = $manager->read(Storage::disk('public')->path($guest->qr_code_path));
            $qrImage->resize(150, 150);
            $canvas->place($qrImage, 'center', 325, $yPosition + 100);
        }
        
        // Add personal message
        $canvas->text("Hi " . $guest->name . "!", 400, 1100, function ($font) use ($themeConfig) {
            $font->file(resource_path('fonts/regular.ttf'));
            $font->size(24);
            $font->color($themeConfig['text_color']);
            $font->align('center');
        });
        
        // Save invitation
        $filename = 'invitations/invitation-' . $guest->slug . '-' . $theme . '.jpg';
        $canvas->save(Storage::disk('public')->path($filename), quality: 90);
        
        return Storage::disk('public')->url($filename);
    }
    
    private function getThemeConfig($theme)
    {
        $themes = [
            'birthday' => [
                'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'title_color' => '#ffffff',
                'text_color' => '#ffffff',
                'url_color' => '#ffd700',
                'decorations' => ['balloons', 'confetti']
            ],
            'princess' => [
                'background' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                'title_color' => '#8b5a87',
                'text_color' => '#6b4a6b',
                'url_color' => '#ff1493',
                'decorations' => ['crown', 'sparkles']
            ],
            'superhero' => [
                'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'title_color' => '#ffff00',
                'text_color' => '#ffffff',
                'url_color' => '#ff4444',
                'decorations' => ['lightning', 'stars']
            ],
            'rainbow' => [
                'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%)',
                'title_color' => '#ffffff',
                'text_color' => '#ffffff', 
                'url_color' => '#ffff00',
                'decorations' => ['rainbow', 'clouds']
            ]
        ];
        
        return $themes[$theme] ?? $themes['birthday'];
    }
    
    private function addDecorations($canvas, $themeConfig)
    {
        // Add emoji decorations based on theme
        $decorations = $themeConfig['decorations'];
        
        if (in_array('confetti', $decorations)) {
            // Add confetti pattern
            for ($i = 0; $i < 50; $i++) {
                $x = rand(0, 800);
                $y = rand(0, 1200);
                $canvas->text('🎊', $x, $y, function ($font) {
                    $font->size(rand(20, 40));
                });
            }
        }
        
        if (in_array('balloons', $decorations)) {
            // Add balloon decorations
            $canvas->text('🎈🎈🎈', 100, 100, function ($font) {
                $font->size(40);
            });
            $canvas->text('🎈🎈🎈', 600, 100, function ($font) {
                $font->size(40);
            });
        }
        
        // Add more decoration logic based on theme...
    }
    
    public function download(Guest $guest, $theme = 'birthday')
    {
        $imageUrl = $this->generateInvitation($guest, $theme);
        $imagePath = Storage::disk('public')->path(str_replace('/storage/', '', $imageUrl));
        
        return response()->download($imagePath, "invitation-{$guest->name}-{$theme}.jpg");
    }
}