@extends('layouts.app')

@section('title', 'Message Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl fun-font text-purple-600 mb-2">Birthday Messages</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Back to Dashboard</a>
    </div>

    @if($messages->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($messages as $message)
                <div class="bg-white rounded-xl shadow-md p-6 {{ !$message->is_approved ? 'opacity-75' : '' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="font-bold text-gray-800">{{ $message->guest->name }}</p>
                            <p class="text-sm text-gray-500">{{ $message->created_at->format('M j, g:i A') }}</p>
                        </div>
                        <form action="{{ route('admin.messages.approve', $message) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" 
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $message->is_approved ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $message->is_approved ? 'Approved ✓' : 'Approve' }}
                            </button>
                        </form>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-700">{{ $message->message }}</p>
                    </div>
                    
                    @if($message->hasMedia())
                        <div class="border-t pt-3 space-y-2">
                            @if($message->photo_path)
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">📷 Photo</p>
                                    <img src="{{ Storage::url($message->photo_path) }}" 
                                         alt="Photo from {{ $message->guest->name }}"
                                         class="rounded-lg max-h-32 cursor-pointer hover:opacity-90"
                                         onclick="window.open(this.src)">
                                </div>
                            @endif
                            
                            @if($message->drawing_path)
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">✏️ Drawing</p>
                                    <img src="{{ Storage::url($message->drawing_path) }}" 
                                         alt="Drawing from {{ $message->guest->name }}"
                                         class="rounded-lg max-h-32 bg-gray-50 cursor-pointer hover:opacity-90"
                                         onclick="window.open(this.src)">
                                </div>
                            @endif
                            
                            @if($message->audio_path)
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">🎤 Audio Message</p>
                                    <audio controls class="w-full">
                                        <source src="{{ Storage::url($message->audio_path) }}" type="audio/mpeg">
                                    </audio>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <p class="text-2xl text-gray-500 mb-2">No messages yet!</p>
            <p class="text-gray-400">Messages will appear here when guests leave them.</p>
        </div>
    @endif
</div>
@endsection