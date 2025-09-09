@extends('layouts.app')

@section('title', 'RSVP Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl fun-font text-purple-600 mb-2">RSVP Management</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Back to Dashboard</a>
    </div>

    <!-- RSVP Stats -->
    <div class="grid md:grid-cols-5 gap-4 mb-8">
        <div class="bg-green-100 rounded-xl p-4 text-center">
            <p class="text-sm text-gray-600">Confirmed</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['confirmed'] }}</p>
        </div>
        <div class="bg-red-100 rounded-xl p-4 text-center">
            <p class="text-sm text-gray-600">Declined</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['declined'] }}</p>
        </div>
        <div class="bg-yellow-100 rounded-xl p-4 text-center">
            <p class="text-sm text-gray-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-blue-100 rounded-xl p-4 text-center">
            <p class="text-sm text-gray-600">Adults</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['adults_total'] }}</p>
        </div>
        <div class="bg-purple-100 rounded-xl p-4 text-center">
            <p class="text-sm text-gray-600">Children</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['children_total'] }}</p>
        </div>
    </div>

    <!-- RSVP List -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-xl font-bold text-gray-800">RSVP Details</h2>
        </div>
        
        @if($rsvps->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dietary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responded</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rsvps as $rsvp)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $rsvp->guest->name }}</div>
                            <div class="text-sm text-gray-500">
                                {{ $rsvp->guest->parent_name ? 'Parent: ' . $rsvp->guest->parent_name : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $rsvp->status == 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($rsvp->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($rsvp->status == 'confirmed')
                                👨 {{ $rsvp->adults_attending }} | 👶 {{ $rsvp->children_attending }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $rsvp->dietary_restrictions ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $rsvp->responded_at->format('M j, g:i A') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-500">
            No RSVPs received yet.
        </div>
        @endif
    </div>

    <!-- Dietary Restrictions Summary -->
    @php
        $dietaryRestrictions = $rsvps->where('status', 'confirmed')
            ->whereNotNull('dietary_restrictions')
            ->pluck('dietary_restrictions');
    @endphp
    
    @if($dietaryRestrictions->count() > 0)
    <div class="mt-8 bg-yellow-50 rounded-xl shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">⚠️ Dietary Restrictions</h3>
        <ul class="space-y-2">
            @foreach($dietaryRestrictions as $restriction)
            <li class="flex items-start">
                <span class="text-yellow-600 mr-2">•</span>
                <span>{{ $restriction }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection