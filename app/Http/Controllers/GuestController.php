<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\PartyDetail;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function show($unique_url)
    {
        $guest = Guest::where('unique_url', $unique_url)->firstOrFail();
        $partyDetails = PartyDetail::first();
        
        if (!$partyDetails) {
            abort(503, 'Party details not configured yet.');
        }

        return view('guest.landing', compact('guest', 'partyDetails'));
    }
    
    public function switchLanguage(Request $request, $unique_url)
    {
        $guest = Guest::where('unique_url', $unique_url)->firstOrFail();
        
        $validated = $request->validate([
            'language' => 'required|in:en,nl'
        ]);
        
        // Update guest's preferred language
        $guest->update(['preferred_language' => $validated['language']]);
        
        // Set the locale for the current request
        app()->setLocale($validated['language']);
        
        // Store in session for persistence
        session(['locale' => $validated['language']]);
        
        return redirect()->back()->with('success', __('messages.language_switched'));
    }
}
