<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GiftController;

// Public routes
Route::get('/', function () {
    // Set locale from session or default to 'en'
    $locale = session('locale', 'en');
    app()->setLocale($locale);
    return view('home');
})->name('home');

// Language switching for homepage
Route::post('/switch-language', function (Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'language' => 'required|in:en,nl'
    ]);
    
    app()->setLocale($validated['language']);
    session(['locale' => $validated['language']]);
    
    return redirect()->back();
})->name('switch-language');

// Guest personalized landing page
Route::get('/invite/{unique_url}', [GuestController::class, 'show'])->name('guest.landing');

// Language switching
Route::post('/invite/{unique_url}/language', [GuestController::class, 'switchLanguage'])->name('guest.switch-language');

// RSVP routes
Route::post('/rsvp/{guest}', [RsvpController::class, 'store'])->name('rsvp.store');
Route::put('/rsvp/{guest}', [RsvpController::class, 'update'])->name('rsvp.update');

// Message/Guestbook routes
Route::post('/message/{guest}', [MessageController::class, 'store'])->name('message.store');
Route::get('/guestbook', [MessageController::class, 'index'])->name('guestbook');

// Gift registry routes
Route::get('/gifts', [GiftController::class, 'index'])->name('gifts.index');
Route::post('/gifts/{gift}/claim', [GiftController::class, 'claim'])->name('gifts.claim');

// Admin routes (protected with basic auth)
Route::prefix('admin')->name('admin.')->middleware('auth.basic.custom')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/guests', [AdminController::class, 'guests'])->name('guests');
    Route::post('/guests', [AdminController::class, 'storeGuest'])->name('guests.store');
    Route::post('/guests/import', [AdminController::class, 'importGuests'])->name('guests.import');
    Route::get('/guests/sample-csv', [AdminController::class, 'downloadSampleCsv'])->name('guests.sample-csv');
    Route::get('/guests/export', [AdminController::class, 'exportGuests'])->name('guests.export');
    Route::get('/rsvps', [AdminController::class, 'rsvps'])->name('rsvps');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::put('/messages/{message}/approve', [AdminController::class, 'approveMessage'])->name('messages.approve');
    Route::get('/party-details', [AdminController::class, 'partyDetails'])->name('party-details');
    Route::put('/party-details', [AdminController::class, 'updatePartyDetails'])->name('party-details.update');
    Route::post('/guests/{guest}/generate-qr', [AdminController::class, 'generateQrCode'])->name('guests.generate-qr');
    Route::post('/guests/generate-all-qr', [AdminController::class, 'generateAllQrCodes'])->name('guests.generate-all-qr');
    
    // Gift management routes
    Route::get('/gifts', [GiftController::class, 'adminIndex'])->name('gifts.index');
    Route::post('/gifts', [GiftController::class, 'store'])->name('gifts.store');
    Route::put('/gifts/{gift}', [GiftController::class, 'update'])->name('gifts.update');
    Route::post('/gifts/{gift}/toggle', [GiftController::class, 'toggleStatus'])->name('gifts.toggle');
    Route::post('/gifts/{gift}/unclaim', [GiftController::class, 'unclaim'])->name('gifts.unclaim');
    Route::delete('/gifts/{gift}', [GiftController::class, 'destroy'])->name('gifts.destroy');
});
