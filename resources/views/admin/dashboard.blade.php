@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl fun-font text-purple-600 mb-2">Admin Dashboard</h1>
        <p class="text-gray-600">Manage {{ $partyDetails->child_name }}'s Birthday Party</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Guests</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_guests'] }}</p>
                </div>
                <span class="text-4xl">👥</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Confirmed RSVPs</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['confirmed_rsvps'] }}</p>
                </div>
                <span class="text-4xl">✅</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Attendees</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_attendees'] }}</p>
                </div>
                <span class="text-4xl">🎉</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Messages</p>
                    <p class="text-3xl font-bold text-pink-600">{{ $stats['total_messages'] }}</p>
                    @if($stats['unapproved_messages'] > 0)
                        <p class="text-xs text-orange-500">{{ $stats['unapproved_messages'] }} pending</p>
                    @endif
                </div>
                <span class="text-4xl">💌</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('admin.guests') }}" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-xl p-6 hover:shadow-lg transition-shadow">
            <h3 class="text-xl font-bold mb-2">Manage Guests</h3>
            <p>Add new guests and view their unique invitation links</p>
        </a>

        <a href="{{ route('admin.rsvps') }}" class="bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-xl p-6 hover:shadow-lg transition-shadow">
            <h3 class="text-xl font-bold mb-2">View RSVPs</h3>
            <p>See who's coming and dietary restrictions</p>
        </a>

        <a href="{{ route('admin.messages') }}" class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl p-6 hover:shadow-lg transition-shadow">
            <h3 class="text-xl font-bold mb-2">Birthday Messages</h3>
            <p>Review and approve guestbook messages</p>
        </a>

        <a href="{{ route('admin.gifts.index') }}" class="bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl p-6 hover:shadow-lg transition-shadow">
            <h3 class="text-xl font-bold mb-2">Gift Registry</h3>
            <p>Manage birthday wishlist and track claimed gifts</p>
        </a>
    </div>

    <!-- Party Details Summary -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Party Details</h2>
            <a href="{{ route('admin.party-details') }}" class="text-purple-600 hover:text-purple-700">Edit →</a>
        </div>
        
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600"><strong>Date:</strong> {{ $partyDetails->getFormattedDate() }}</p>
                <p class="text-gray-600"><strong>Time:</strong> {{ $partyDetails->getFormattedTime() }}</p>
                <p class="text-gray-600"><strong>Venue:</strong> {{ $partyDetails->venue_name }}</p>
            </div>
            <div>
                <p class="text-gray-600"><strong>Theme:</strong> {{ $partyDetails->theme ?? 'Not set' }}</p>
                <p class="text-gray-600"><strong>Days Until Party:</strong> 
                    @if($partyDetails->getDaysUntilParty() > 0)
                        <span class="text-green-600">{{ $partyDetails->getDaysUntilParty() }} days</span>
                    @elseif($partyDetails->getDaysUntilParty() == 0)
                        <span class="text-purple-600 font-bold">TODAY! 🎉</span>
                    @else
                        <span class="text-gray-500">Party has passed</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8 text-center">
        <a href="/" class="text-gray-600 hover:text-gray-800">← Back to Home</a>
    </div>
</div>
@endsection