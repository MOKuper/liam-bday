<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Message;
use App\Models\PartyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with('guest')
            ->approved()
            ->latest()
            ->get();
            
        $partyDetails = PartyDetail::first();
        
        return view('guestbook', compact('messages', 'partyDetails'));
    }
    
    public function store(Request $request, Guest $guest)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'drawing' => 'nullable|image|max:5120', // 5MB max
                'photo' => 'nullable|image|max:5120',
                'audio' => 'nullable|file|max:2048', // 2MB max (within PHP limits)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                $errors = $e->errors();
                $message = 'Controleer je invoer en probeer het opnieuw.';
                
                // Add debug information for audio files
                $debugInfo = [];
                if ($request->hasFile('audio')) {
                    $audioFile = $request->file('audio');
                    $debugInfo['audio_debug'] = [
                        'original_name' => $audioFile->getClientOriginalName(),
                        'mime_type' => $audioFile->getMimeType(),
                        'size' => $audioFile->getSize(),
                        'extension' => $audioFile->getClientOriginalExtension(),
                        'is_valid' => $audioFile->isValid(),
                    ];
                }
                
                // Provide specific error messages for common issues
                if (isset($errors['audio'])) {
                    $message = 'Audio probleem: ' . implode(', ', $errors['audio']) . 
                              ($debugInfo ? ' | Debug: ' . json_encode($debugInfo) : '');
                } elseif (isset($errors['photo']) || isset($errors['drawing'])) {
                    $message = 'Het bestand is te groot of heeft een ongeldig formaat. Probeer een kleiner bestand.';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => $errors,
                    'debug' => $debugInfo
                ], 422);
            }
            throw $e;
        }
        
        try {
            $messageData = [
                'guest_id' => $guest->id,
                'message' => $validated['message'],
                'is_approved' => true, // Auto-approve for now, can be changed to false for moderation
            ];
            
            // Handle file uploads
            if ($request->hasFile('drawing')) {
                $messageData['drawing_path'] = $request->file('drawing')->store('drawings', 'public');
            }
            
            if ($request->hasFile('photo')) {
                $messageData['photo_path'] = $request->file('photo')->store('photos', 'public');
            }
            
            if ($request->hasFile('audio')) {
                $messageData['audio_path'] = $request->file('audio')->store('audio', 'public');
            }
            
            $message = Message::create($messageData);
            
            // Check if it's an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Je verjaardagsbericht is verstuurd! Bedankt!',
                    'data' => [
                        'id' => $message->id,
                        'message' => $message->message,
                        'has_files' => !empty($message->drawing_path) || !empty($message->photo_path) || !empty($message->audio_path)
                    ]
                ]);
            }
            
            return back()->with('success', 'Your birthday message has been sent! Thank you!');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Er is een fout opgetreden. Probeer het later opnieuw.'
                ], 500);
            }
            
            return back()->with('error', 'Er is een fout opgetreden. Probeer het later opnieuw.');
        }
    }
}
