<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GiftController extends Controller
{
    // Public gift registry page
    public function index()
    {
        $gifts = Gift::active()
            ->orderBy('priority')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
            
        $stats = [
            'total_gifts' => Gift::active()->count(),
            'claimed_gifts' => Gift::active()->claimed()->count(),
            'available_gifts' => Gift::active()->available()->count(),
        ];
        
        return view('gifts.index', compact('gifts', 'stats'));
    }

    // Claim a gift
    public function claim(Request $request, Gift $gift)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($gift->is_claimed) {
            return back()->with('error', 'This gift has already been claimed by someone else.');
        }

        $gift->claimGift($request->name, $request->email);

        return back()->with('success', 'Thank you! You have successfully claimed: ' . $gift->name);
    }

    // Admin: List all gifts
    public function adminIndex()
    {
        $gifts = Gift::latest()->get();
        
        $stats = [
            'total' => Gift::count(),
            'claimed' => Gift::claimed()->count(),
            'available' => Gift::available()->count(),
            'high_priority' => Gift::where('priority', 1)->count(),
        ];
        
        return view('admin.gifts.index', compact('gifts', 'stats'));
    }

    // Admin: Store new gift
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'price_range_min' => 'nullable|numeric|min:0',
            'price_range_max' => 'nullable|numeric|min:0|gte:price_range_min',
            'store_suggestion' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:500',
            'priority' => 'required|integer|in:1,2,3',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Gift::create($validated);

        return back()->with('success', 'Gift added to the registry successfully!');
    }

    // Admin: Update gift
    public function update(Request $request, Gift $gift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'price_range_min' => 'nullable|numeric|min:0',
            'price_range_max' => 'nullable|numeric|min:0|gte:price_range_min',
            'store_suggestion' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:500',
            'priority' => 'required|integer|in:1,2,3',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $gift->update($validated);

        return back()->with('success', 'Gift updated successfully!');
    }

    // Admin: Toggle gift status
    public function toggleStatus(Gift $gift)
    {
        $gift->update(['is_active' => !$gift->is_active]);
        
        $status = $gift->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Gift has been {$status}.");
    }

    // Admin: Unclaim gift
    public function unclaim(Gift $gift)
    {
        if (!$gift->is_claimed) {
            return back()->with('error', 'This gift is not currently claimed.');
        }

        $gift->unclaimGift();

        return back()->with('success', 'Gift has been unclaimed and is now available again.');
    }

    // Admin: Delete gift
    public function destroy(Gift $gift)
    {
        $giftName = $gift->name;
        $gift->delete();

        return back()->with('success', "Gift '{$giftName}' has been deleted.");
    }
}
