@extends('layouts.app')

@section('title', "Birthday Guestbook")

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-4xl md:text-6xl fun-font text-purple-600 mb-4">
            Birthday Guestbook 📖
        </h1>
        <p class="text-xl text-gray-700">Messages from friends for {{ $partyDetails->child_name }}'s {{ $partyDetails->child_age }}th birthday!</p>
    </div>

    @if($messages->count() > 0)
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($messages as $message)
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center mb-3">
                        <div class="bg-gradient-to-r from-purple-400 to-pink-400 rounded-full w-12 h-12 flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($message->guest->name, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="font-bold text-gray-800">{{ $message->guest->name }}</p>
                            <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-700">{{ $message->message }}</p>
                    </div>
                    
                    @if($message->hasMedia())
                        <div class="border-t pt-3 space-y-2">
                            @if($message->photo_path)
                                <div>
                                    <img src="{{ Storage::url($message->photo_path) }}" 
                                         alt="Photo from {{ $message->guest->name }}"
                                         class="rounded-lg max-h-48 w-full object-cover cursor-pointer hover:opacity-90"
                                         onclick="window.open(this.src)">
                                </div>
                            @endif
                            
                            @if($message->drawing_path)
                                <div>
                                    <img src="{{ Storage::url($message->drawing_path) }}" 
                                         alt="Drawing from {{ $message->guest->name }}"
                                         class="rounded-lg max-h-48 w-full object-contain bg-gray-50 cursor-pointer hover:opacity-90"
                                         onclick="window.open(this.src)">
                                </div>
                            @endif
                            
                            @if($message->audio_path)
                                <div>
                                    <audio controls class="w-full">
                                        <source src="{{ Storage::url($message->audio_path) }}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="max-w-2xl mx-auto text-center">
            <div class="bg-yellow-50 rounded-3xl p-12">
                <p class="text-2xl text-gray-600 mb-4">No messages yet!</p>
                <p class="text-lg text-gray-500">Be the first to leave a birthday message!</p>
            </div>
        </div>
    @endif
    
    <div class="text-center mt-12">
        <a href="/" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-full transform hover:scale-105 transition-all">
            Back to Home
        </a>
    </div>
</div>
@endsection