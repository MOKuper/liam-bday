@extends('layouts.app')

@section('title', 'Gift Registry - Liam\'s 5th Birthday')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="text-center mb-8">
        <h1 class="text-4xl md:text-6xl fun-font text-purple-600 mb-4">
            🎁 Liam's Birthday Wishlist 🎁
        </h1>
        <p class="text-xl text-gray-700 mb-6">
            Help make Liam's 5th birthday extra special! Choose a gift from his wishlist and claim it so we avoid duplicates.
        </p>
        
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto">
            <div class="bg-purple-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['total_gifts'] }}</div>
                <div class="text-sm text-purple-700">Total Gifts</div>
            </div>
            <div class="bg-green-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-green-600">{{ $stats['available_gifts'] }}</div>
                <div class="text-sm text-green-700">Available</div>
            </div>
            <div class="bg-orange-100 rounded-xl p-4">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['claimed_gifts'] }}</div>
                <div class="text-sm text-orange-700">Already Claimed</div>
            </div>
        </div>
    </div>

    @if($gifts->isEmpty())
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🎁</div>
            <h3 class="text-2xl font-bold text-gray-700 mb-2">No gifts in the registry yet!</h3>
            <p class="text-gray-600">Check back soon - Liam's wishlist will be updated.</p>
        </div>
    @else
        <!-- Gift Categories -->
        @foreach($gifts as $category => $categoryGifts)
            <div class="mb-12">
                <h2 class="text-3xl fun-font text-pink-500 mb-6 text-center">
                    @if($category)
                        {{ $category }}
                    @else
                        Other Gifts
                    @endif
                </h2>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categoryGifts as $gift)
                        <div class="bg-white rounded-xl shadow-lg p-6 {{ $gift->is_claimed ? 'opacity-75' : 'hover:shadow-xl transition-shadow' }}">
                            <!-- Gift Image -->
                            @if($gift->image_url)
                                <div class="mb-4">
                                    <img src="{{ $gift->image_url }}" alt="{{ $gift->name }}" 
                                         class="w-full h-48 object-cover rounded-lg">
                                </div>
                            @else
                                <div class="mb-4 w-full h-48 bg-gradient-to-br from-purple-200 to-pink-200 rounded-lg flex items-center justify-center">
                                    <span class="text-6xl">🎁</span>
                                </div>
                            @endif
                            
                            <!-- Priority Badge -->
                            <div class="flex justify-between items-start mb-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $gift->priority_color }}">
                                    {{ $gift->priority_text }} Priority
                                </span>
                                
                                @if($gift->is_claimed)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        ✅ Claimed
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Gift Details -->
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $gift->name }}</h3>
                            
                            @if($gift->description)
                                <p class="text-gray-600 mb-3">{{ $gift->description }}</p>
                            @endif
                            
                            <!-- Price Range -->
                            @if($gift->price_range_text)
                                <p class="text-lg font-semibold text-purple-600 mb-3">{{ $gift->price_range_text }}</p>
                            @endif
                            
                            <!-- Store Suggestion -->
                            @if($gift->store_suggestion)
                                <p class="text-sm text-gray-500 mb-4">
                                    <strong>Where to buy:</strong> {{ $gift->store_suggestion }}
                                </p>
                            @endif
                            
                            <!-- Claim Status -->
                            @if($gift->is_claimed)
                                <div class="text-center">
                                    <div class="bg-gray-100 text-gray-600 py-3 px-4 rounded-lg">
                                        <strong>This gift has been claimed!</strong><br>
                                        <span class="text-sm">Thank you {{ $gift->claimed_by_name }}! 💝</span>
                                    </div>
                                </div>
                            @else
                                <!-- Claim Form -->
                                <form action="{{ route('gifts.claim', $gift) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                                        <input type="text" name="name" required 
                                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                               placeholder="Enter your name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Email *</label>
                                        <input type="email" name="email" required 
                                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                               placeholder="your@email.com">
                                    </div>
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-3 px-4 rounded-lg hover:shadow-lg transform hover:scale-105 transition-all">
                                        🎉 Claim This Gift!
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    <!-- Call to Action -->
    <div class="bg-gradient-to-r from-purple-100 to-pink-100 rounded-xl p-8 text-center mt-12">
        <h3 class="text-2xl fun-font text-purple-700 mb-4">Thank You for Making Liam's Day Special! 🎈</h3>
        <p class="text-purple-600 mb-4">
            Once you claim a gift, you'll receive a confirmation and we'll mark it as taken to avoid duplicates.
            You can bring the gift to the party or coordinate with us for delivery.
        </p>
        <div class="space-x-4">
            <a href="/" class="inline-block bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-full">
                🏠 Back to Home
            </a>
            <a href="{{ route('guestbook') }}" class="inline-block bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-6 rounded-full">
                💌 Leave a Message
            </a>
        </div>
    </div>

    <!-- Contact Info -->
    <div class="text-center mt-8 text-gray-600">
        <p>Questions about gifts? Contact us at the party details provided in your invitation!</p>
    </div>
</div>

@push('scripts')
<script>
    // Show success/error messages
    @if(session('success'))
        // Create and show success notification
        const successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
        successDiv.innerHTML = '✅ {{ session('success') }}';
        document.body.appendChild(successDiv);
        
        setTimeout(() => {
            successDiv.remove();
        }, 5000);
    @endif
    
    @if(session('error'))
        // Create and show error notification
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
        errorDiv.innerHTML = '❌ {{ session('error') }}';
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    @endif

    // Confetti when claiming a gift
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Simple confetti effect
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.innerHTML = ['🎉', '🎊', '🎁', '💝', '✨'][Math.floor(Math.random() * 5)];
                    confetti.style.position = 'fixed';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.top = '-20px';
                    confetti.style.fontSize = '20px';
                    confetti.style.pointerEvents = 'none';
                    confetti.style.zIndex = '1000';
                    confetti.style.animation = 'fall 3s linear forwards';
                    
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => {
                        if (confetti.parentNode) {
                            confetti.remove();
                        }
                    }, 3000);
                }, i * 50);
            }
        });
    });

    // Add fall animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush
@endsection