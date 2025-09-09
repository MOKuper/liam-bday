@extends('layouts.app')

@section('title', 'Party Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl fun-font text-purple-600 mb-2">Party Details</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Back to Dashboard</a>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-8">
        <form action="{{ route('admin.party-details.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Basic Information</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Child's Name</label>
                        <input type="text" name="child_name" value="{{ $partyDetails->child_name }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Child's Age</label>
                        <input type="number" name="child_age" value="{{ $partyDetails->child_age }}" min="1" max="20" required
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Party Date</label>
                        <input type="date" name="party_date" value="{{ $partyDetails->party_date->format('Y-m-d') }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                            <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($partyDetails->start_time)->format('H:i') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                            <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($partyDetails->end_time)->format('H:i') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>
                </div>
                
                <!-- Venue Info -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Venue Information</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue Name</label>
                        <input type="text" name="venue_name" value="{{ $partyDetails->venue_name }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue Address</label>
                        <textarea name="venue_address" rows="2" required
                                  class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">{{ $partyDetails->venue_address }}</textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Map URL (optional)</label>
                        <input type="url" name="venue_map_url" value="{{ $partyDetails->venue_map_url }}"
                               placeholder="https://maps.google.com/..."
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parking Information (optional)</label>
                        <textarea name="parking_info" rows="2"
                                  class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">{{ $partyDetails->parking_info }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Additional Details -->
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Additional Details</h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Party Theme (optional)</label>
                        <input type="text" name="theme" value="{{ $partyDetails->theme }}"
                               placeholder="e.g., Dinosaur Adventure"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent Contact Information</label>
                        <input type="text" name="parent_contact_info" value="{{ $partyDetails->parent_contact_info }}" required
                               placeholder="e.g., Mom: (555) 123-4567 | Dad: (555) 987-6543"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Activities (optional)</label>
                        <textarea name="activities" rows="3"
                                  placeholder="List the fun activities planned for the party"
                                  class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">{{ $partyDetails->activities }}</textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gift Suggestions (optional)</label>
                        <textarea name="gift_suggestions" rows="3"
                                  placeholder="What does the birthday child like?"
                                  class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">{{ $partyDetails->gift_suggestions }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-8 rounded-full transform hover:scale-105 transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection