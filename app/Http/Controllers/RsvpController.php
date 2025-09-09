<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Rsvp;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    public function store(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,declined',
            'adults_attending' => 'required_if:status,confirmed|integer|min:0|max:10',
            'children_attending' => 'required_if:status,confirmed|integer|min:0|max:10',
            'dietary_restrictions' => 'nullable|string|max:500',
            'special_needs' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['responded_at'] = now();
        
        $guest->rsvp()->updateOrCreate(
            ['guest_id' => $guest->id],
            $validated
        );

        return back()->with('success', 'Thank you for your RSVP!');
    }

    public function update(Request $request, Guest $guest)
    {
        return $this->store($request, $guest);
    }
}
