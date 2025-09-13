@extends('layouts.app')

@section('title', __('messages.welcome') . " {$guest->name}!")

@push('styles')
    <style>
        /* Enhanced animations and styles */
        .fun-font {
            font-family: 'Comic Sans MS', 'Chalkboard SE', 'Marker Felt', cursive;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        /* Floating animation for emojis */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(-5deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .floating-emoji {
            animation: float 3s ease-in-out infinite;
            display: inline-block;
        }

        /* Staggered floating for multiple emojis */
        .floating-emoji:nth-child(2) { animation-delay: 0.2s; }
        .floating-emoji:nth-child(3) { animation-delay: 0.4s; }
        .floating-emoji:nth-child(4) { animation-delay: 0.6s; }
        .floating-emoji:nth-child(5) { animation-delay: 0.8s; }

        /* Gradient text animation */
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .gradient-text {
            background: linear-gradient(270deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #ffa07a);
            background-size: 400% 400%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 8s ease infinite;
        }

        /* Pulse animation for buttons */
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(236, 72, 153, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(236, 72, 153, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(236, 72, 153, 0); }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        /* Card hover effects */
        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-10px) rotate(1deg);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Confetti background */
        .confetti-bg {
            position: relative;
            overflow: hidden;
        }

        .confetti-bg::before {
            content: '🎉 🎊 🎈 🎁 🌟 ✨ 🎂 🍰';
            position: absolute;
            top: -100%;
            left: 0;
            width: 100%;
            height: 200%;
            font-size: 30px;
            opacity: 0.1;
            animation: confetti-rain 20s linear infinite;
        }

        @keyframes confetti-rain {
            to { transform: translateY(100%); }
        }

        /* Enhanced card shadows and gradients */
        .party-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        /* Wiggle animation for icons */
        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }

        .icon-wiggle:hover {
            animation: wiggle 0.5s ease-in-out;
        }

        /* Rainbow border animation */
        @keyframes rainbow-border {
            0% { border-color: #ff6b6b; }
            14% { border-color: #ffa07a; }
            28% { border-color: #ffd93d; }
            42% { border-color: #6bcf7c; }
            56% { border-color: #4ecdc4; }
            70% { border-color: #45b7d1; }
            84% { border-color: #dda0dd; }
            100% { border-color: #ff6b6b; }
        }

        .rainbow-border {
            border: 3px solid;
            animation: rainbow-border 4s linear infinite;
        }

        /* Sparkle effect */
        @keyframes sparkle {
            0%, 100% { opacity: 0; transform: scale(0); }
            50% { opacity: 1; transform: scale(1); }
        }

        .sparkle {
            position: absolute;
            animation: sparkle 1.5s ease-in-out infinite;
        }

        /* Enhanced form inputs */
        input[type="radio"]:checked + span {
            font-weight: bold;
            color: #10b981;
        }

        input[type="radio"]:checked + span::after {
            content: ' ✓';
            color: #10b981;
        }

        /* Loading animation */
        @keyframes bounce-delay {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        .loading-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #ec4899;
            border-radius: 50%;
            animation: bounce-delay 1.4s infinite ease-in-out both;
        }

        .loading-dot:nth-child(1) { animation-delay: -0.32s; }
        .loading-dot:nth-child(2) { animation-delay: -0.16s; }
        .loading-dot:nth-child(3) { animation-delay: 0; }

        /* Party theme background pattern */
        .party-pattern {
            background-image:
                radial-gradient(circle at 20% 80%, rgba(255, 107, 107, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(78, 205, 196, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 160, 122, 0.1) 0%, transparent 50%);
        }
    </style>
@endpush

@section('content')
    @php
        // Set locale based on guest's preferred language
        if ($guest->preferred_language) {
            app()->setLocale($guest->preferred_language);
        }
    @endphp
    <div class="container mx-auto px-4 py-8 party-pattern min-h-screen">
        <!-- Animated particles background -->
        <div id="particles-container" class="fixed inset-0 pointer-events-none z-0"></div>

        <!-- Language Switcher with better animation -->
        <div class="fixed top-4 right-4 z-50">
            <form action="{{ route('guest.switch-language', $guest->unique_url) }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <button type="submit" name="language" value="{{ $guest->preferred_language == 'nl' ? 'en' : 'nl' }}"
                        class="bg-white bg-opacity-95 hover:bg-opacity-100 shadow-lg rounded-full px-3 py-2 md:px-4 md:py-2 flex items-center space-x-2 transition-all hover:scale-110 hover:rotate-3 border border-gray-200">
                    @if($guest->preferred_language == 'nl')
                        <span class="text-lg md:text-xl floating-emoji">🇬🇧</span>
                        <span class="font-medium text-gray-700 text-sm md:text-base">English</span>
                    @else
                        <span class="text-lg md:text-xl floating-emoji">🇳🇱</span>
                        <span class="font-medium text-gray-700 text-sm md:text-base">Nederlands</span>
                    @endif
                </button>
            </form>
        </div>

        <!-- Enhanced Hero Section -->
        <div class="relative mb-8 rounded-3xl overflow-hidden shadow-2xl hover-lift confetti-bg {{ $guest->friendship_photo_path ? '' : 'bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400' }}"
             @if($guest->friendship_photo_path)
                 style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('{{ Storage::url($guest->friendship_photo_path) }}');
                background-size: cover;
                background-position: center;
                min-height: 400px;"
             @else
                 style="min-height: 300px; background-size: 400% 400%; animation: gradient-shift 15s ease infinite;"
            @endif>

            <!-- Sparkle effects -->
            <div class="sparkle" style="top: 20%; left: 10%; animation-delay: 0s;">✨</div>
            <div class="sparkle" style="top: 60%; right: 15%; animation-delay: 0.5s;">⭐</div>
            <div class="sparkle" style="bottom: 30%; left: 20%; animation-delay: 1s;">🌟</div>

            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            <div class="relative z-10 flex items-center justify-center h-full py-16 px-8 text-center">
                <div>
                    <h1 class="text-4xl md:text-6xl fun-font text-white mb-4 drop-shadow-lg">
                        <span class="gradient-text">{{ __('messages.hello') }}</span> <span class="text-white">{{ $guest->name }}!</span>
                        <span class="floating-emoji text-5xl md:text-7xl">👋</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-white drop-shadow-lg mb-6">
                        {{ __('messages.invitation_sentence', ['age' => $partyDetails->child_age]) }}
                    </p>

                    @if(!$guest->friendship_photo_path)
                        <div class="mt-6 text-5xl md:text-6xl">
                            <span class="floating-emoji">🎉</span>
                            <span class="floating-emoji">🎂</span>
                            <span class="floating-emoji">🎈</span>
                            <span class="floating-emoji">🎁</span>
                            <span class="floating-emoji">🦕</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enhanced Party Details Card -->
        <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6 mb-8">
            <!-- Left Column - Party Info with enhanced styling -->
            <div class="party-card rounded-3xl shadow-xl p-6 hover-lift rainbow-border">
                <h2 class="text-3xl fun-font gradient-text mb-6">{{ __('messages.party_details') }} 🎉</h2>

                <div class="space-y-4">
                    <div class="flex items-center group">
                        <span class="text-3xl mr-3 icon-wiggle">📅</span>
                        <div class="group-hover:translate-x-2 transition-transform">
                            <p class="font-bold text-gray-800">{{ __('messages.date') }}</p>
                            <p class="text-lg">{{ $partyDetails->getFormattedDate() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center group">
                        <span class="text-3xl mr-3 icon-wiggle">⏰</span>
                        <div class="group-hover:translate-x-2 transition-transform">
                            <p class="font-bold text-gray-800">{{ __('messages.time') }}</p>
                            <p class="text-lg">{{ $partyDetails->getFormattedTime() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center group">
                        <span class="text-3xl mr-3 icon-wiggle">📍</span>
                        <div class="group-hover:translate-x-2 transition-transform">
                            <p class="font-bold text-gray-800">{{ __('messages.location') }}</p>
                            <p class="text-lg">{{ $partyDetails->venue_name }}</p>
                            <p class="text-sm text-gray-600">{{ $partyDetails->venue_address }}</p>
                            @if($partyDetails->venue_map_url)
                                <a href="{{ $partyDetails->venue_map_url }}" target="_blank"
                                   class="text-blue-500 hover:text-blue-700 hover:underline text-sm inline-flex items-center gap-1 transition-all">
                                    {{ __('messages.view_on_map') }}
                                    <span class="transition-transform group-hover:translate-x-1">→</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($partyDetails->theme)
                        <div class="flex items-center group">
                            <span class="text-3xl mr-3 icon-wiggle">🦕</span>
                            <div class="group-hover:translate-x-2 transition-transform">
                                <p class="font-bold text-gray-800">{{ __('messages.theme') }}</p>
                                <p class="text-lg gradient-text font-bold">{{ $partyDetails->theme }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Enhanced RSVP -->
            <div class="bg-gradient-to-br from-purple-100 via-pink-100 to-yellow-100 rounded-3xl shadow-xl p-6 hover-lift border-2 border-purple-200">
                <h2 class="text-3xl fun-font gradient-text mb-6">{{ __('messages.rsvp') }} 📮</h2>

                @if($guest->rsvp)
                    <div class="bg-white rounded-2xl p-4 mb-4 shadow-inner">
                        <p class="text-lg mb-2">{{ __('messages.current_status') }}:
                            <span class="font-bold {{ $guest->rsvp->status == 'confirmed' ? 'text-green-600' : ($guest->rsvp->status == 'declined' ? 'text-red-600' : 'text-yellow-600') }}">
                            @if($guest->rsvp->status == 'confirmed')
                                    ✅ {{ __('messages.confirmed') }}
                                @elseif($guest->rsvp->status == 'declined')
                                    ❌ {{ __('messages.declined') }}
                                @elseif($guest->rsvp->status == 'unsure')
                                    🤔 {{ __('messages.unsure_attending') }}
                                @else
                                    ⏳ {{ __('messages.pending') }}
                                @endif
                        </span>
                        </p>
                        @if($guest->rsvp->status == 'confirmed')
                            <p class="flex items-center gap-2">
                                <span class="text-2xl">👥</span>
                                {{ __('messages.attending') }}: {{ $guest->rsvp->adults_attending }} {{ __('messages.adults') }}, {{ $guest->rsvp->children_attending }} {{ __('messages.children') }}
                            </p>
                        @endif
                    </div>
                @endif

                <form action="{{ route('rsvp.store', $guest) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-lg font-bold mb-3">{{ __('messages.will_you_attend') }}</label>
                        <div class="space-y-3">
                            <label class="flex items-center bg-white rounded-lg p-4 hover:bg-green-50 cursor-pointer transition-all hover:scale-105 shadow-sm">
                                <input type="radio" name="status" value="confirmed" class="mr-3 w-5 h-5 text-green-600"
                                    {{ $guest->rsvp && $guest->rsvp->status == 'confirmed' ? 'checked' : '' }}>
                                <span class="text-lg">{{ __('messages.yes_attending') }}</span>
                            </label>
                            <label class="flex items-center bg-white rounded-lg p-4 hover:bg-yellow-50 cursor-pointer transition-all hover:scale-105 shadow-sm">
                                <input type="radio" name="status" value="unsure" class="mr-3 w-5 h-5 text-yellow-600"
                                    {{ $guest->rsvp && $guest->rsvp->status == 'unsure' ? 'checked' : '' }}>
                                <span class="text-lg">{{ __('messages.unsure_attending') }}</span>
                            </label>
                            <label class="flex items-center bg-white rounded-lg p-4 hover:bg-red-50 cursor-pointer transition-all hover:scale-105 shadow-sm">
                                <input type="radio" name="status" value="declined" class="mr-3 w-5 h-5 text-red-600"
                                    {{ $guest->rsvp && $guest->rsvp->status == 'declined' ? 'checked' : '' }}>
                                <span class="text-lg">{{ __('messages.no_attending') }}</span>
                            </label>
                        </div>
                    </div>

                    <div id="attendee-details" class="{{ !$guest->rsvp || $guest->rsvp->status != 'confirmed' ? 'hidden' : '' }} transition-all">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-bold mb-2 flex items-center gap-1">
                                    <span>{{ __('messages.adults') }}</span>
                                    <span class="text-xl">👨‍👩‍👧</span>
                                </label>
                                <input type="number" name="adults_attending" min="0" max="10" value="{{ $guest->rsvp ? $guest->rsvp->adults_attending : 1 }}"
                                       class="w-full rounded-lg border-2 border-purple-300 focus:border-purple-500 focus:ring-purple-500 p-2">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2 flex items-center gap-1">
                                    <span>{{ __('messages.children') }}</span>
                                    <span class="text-xl">👦👧</span>
                                </label>
                                <input type="number" name="children_attending" min="0" max="10" value="{{ $guest->rsvp ? $guest->rsvp->children_attending : 1 }}"
                                       class="w-full rounded-lg border-2 border-purple-300 focus:border-purple-500 focus:ring-purple-500 p-2">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 flex items-center gap-1">
                                <span>{{ __('messages.dietary_restrictions') }}</span>
                                <span class="text-xl">🥗</span>
                            </label>
                            <textarea name="dietary_restrictions" rows="2"
                                      class="w-full rounded-lg border-2 border-purple-300 focus:border-purple-500 focus:ring-purple-500 p-2"
                                      placeholder="{{ __('messages.dietary_placeholder') }}">{{ $guest->rsvp ? $guest->rsvp->dietary_restrictions : '' }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-3 px-6 rounded-full hover:shadow-lg transform hover:scale-105 transition-all pulse-animation">
                        {{ $guest->rsvp ? __('messages.update_rsvp') : __('messages.submit_rsvp') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Enhanced Message/Guestbook Section -->
        <div class="max-w-4xl mx-auto party-card rounded-3xl shadow-xl p-6 mb-8 hover-lift">
            <h2 class="text-3xl fun-font gradient-text mb-6">{{ __('messages.leave_message') }} 💌</h2>

            @if($guest->messages->count() > 0)
                <div class="bg-gradient-to-r from-green-100 to-green-200 rounded-lg p-4 mb-4 border-2 border-green-300">
                    <p class="text-green-700 font-bold flex items-center gap-2">
                        <span class="text-2xl">🎉</span>
                        {{ __('messages.message_success') }} {{ $partyDetails->child_name }} will love reading it!
                    </p>
                </div>
            @endif

            <form id="message-form" action="{{ route('message.store', $guest) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-lg font-bold mb-3 flex items-center gap-2">
                        <span>{{ __('messages.your_message') }}</span>
                        <span class="text-2xl">✍️</span>
                    </label>
                    <textarea name="message" rows="4" required
                              class="w-full rounded-lg border-2 border-green-300 focus:border-green-500 focus:ring-green-500 p-3 text-lg"
                              placeholder="{{ __('messages.message_placeholder', ['name' => $partyDetails->child_name]) }}"></textarea>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-yellow-50 p-4 rounded-lg border-2 border-yellow-200">
                        <label class="block text-sm font-bold mb-2 flex items-center gap-1">
                            <span>{{ __('messages.upload_drawing') }}</span>
                            <span class="text-xl">🎨</span>
                        </label>
                        <input type="file" name="drawing" accept="image/*" class="text-sm w-full">
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                        <label class="block text-sm font-bold mb-2 flex items-center gap-1">
                            <span>{{ __('messages.upload_photo') }}</span>
                            <span class="text-xl">📸</span>
                        </label>
                        <input type="file" name="photo" accept="image/*" class="text-sm w-full">
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg border-2 border-red-200">
                        <label class="block text-sm font-bold mb-2 flex items-center gap-1">
                            <span>{{ __('messages.record_audio') }}</span>
                            <span class="text-xl">🎤</span>
                        </label>
                        <div id="audio-recorder" class="space-y-2">
                            <button type="button" id="record-button" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all hover:scale-105 w-full">
                                🎤 Start Recording
                            </button>
                            <div id="recording-status" class="text-sm text-gray-600 hidden flex items-center gap-2">
                                <span class="loading-dot"></span>
                                <span class="loading-dot"></span>
                                <span class="loading-dot"></span>
                                <span>Recording... <span id="recording-time">0:00</span></span>
                            </div>
                            <audio id="audio-preview" controls class="w-full hidden"></audio>
                            <input type="file" id="audio-file-input" name="audio" accept="audio/*" class="text-sm hidden">
                        </div>
                    </div>
                </div>

                <button type="submit" id="submit-message" class="bg-gradient-to-r from-green-500 to-emerald-500 text-white font-bold py-3 px-8 rounded-full transform hover:scale-105 transition-all shadow-lg hover:shadow-xl mx-auto block text-lg">
                    {{ __('messages.send_message') }} 🎉
                </button>

                <!-- Success/Error Messages -->
                <div id="message-feedback" class="hidden"></div>
            </form>
        </div>

        <!-- Enhanced Gift Registry Section -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="bg-gradient-to-r from-orange-100 via-yellow-100 to-red-100 rounded-3xl shadow-xl p-8 text-center hover-lift rainbow-border">
                <h3 class="text-3xl fun-font gradient-text mb-4">🎁 {{ __('messages.gift_registry') }} 🎁</h3>
                <div class="mb-6">
                    <span class="floating-emoji text-5xl">🎁</span>
                    <span class="floating-emoji text-5xl">🧸</span>
                    <span class="floating-emoji text-5xl">🎮</span>
                    <span class="floating-emoji text-5xl">📚</span>
                    <span class="floating-emoji text-5xl">🚗</span>
                </div>
                <p class="text-lg text-orange-700 mb-6">
                    {{ __('messages.wish_list_body') }}
                </p>
                <a href="{{ route('gifts.index') }}"
                   class="inline-block bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold py-4 px-8 rounded-full hover:shadow-xl transform hover:scale-110 transition-all text-lg pulse-animation">
                    👀 {{ __('messages.wish_list_button') }}

                </a>
            </div>
        </div>

        <!-- Enhanced Additional Info -->
        <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6 mb-8">
            @if($partyDetails->activities)
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-3xl shadow-xl p-6 hover-lift border-2 border-yellow-300">
                    <h3 class="text-2xl fun-font gradient-text mb-4 flex items-center gap-2">
                        {{ __('messages.activities') }}
                        <span class="floating-emoji">🎪</span>
                    </h3>
                    <p class="text-lg">{{ $partyDetails->activities }}</p>
                </div>
            @endif

            @if($partyDetails->gift_suggestions)
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-3xl shadow-xl p-6 hover-lift border-2 border-blue-300">
                    <h3 class="text-2xl fun-font gradient-text mb-4 flex items-center gap-2">
                        {{ __('messages.gift_ideas') }}
                        <span class="floating-emoji">🎁</span>
                    </h3>
                    <p class="text-lg"> {{ __('messages.gift_ideas_body') }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Enhanced Contact Info -->
        <div class="text-center mt-12 mb-8">
            <div class="inline-block bg-white bg-opacity-90 rounded-full px-8 py-4 shadow-lg">
                <p class="text-gray-700 text-lg">
                    {{ __('messages.questions') }}
                    <span class="font-bold">{{ __('messages.contact') }}: {!! __('messages.parent_contact_info') !!}</span>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Create animated particles background
            function createParticles() {
                const container = document.getElementById('particles-container');
                const particleCount = 30;
                const particles = ['🎈', '🎉', '🎊', '✨', '⭐', '🎁', '🎂'];

                for (let i = 0; i < particleCount; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'absolute opacity-20';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.fontSize = (Math.random() * 20 + 10) + 'px';
                    particle.style.animationDelay = Math.random() * 20 + 's';
                    particle.style.animationDuration = (Math.random() * 20 + 20) + 's';
                    particle.innerHTML = particles[Math.floor(Math.random() * particles.length)];

                    particle.style.animation = `float-particle ${20 + Math.random() * 10}s infinite ease-in-out`;

                    container.appendChild(particle);
                }
            }

            // Add particle animation CSS
            const particleStyle = document.createElement('style');
            particleStyle.textContent = `
        @keyframes float-particle {
            0%, 100% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0.2;
            }
            25% {
                transform: translateY(-100px) translateX(50px) rotate(90deg);
                opacity: 0.4;
            }
            50% {
                transform: translateY(-50px) translateX(-30px) rotate(180deg);
                opacity: 0.3;
            }
            75% {
                transform: translateY(-150px) translateX(-50px) rotate(270deg);
                opacity: 0.5;
            }
        }
    `;
            document.head.appendChild(particleStyle);

            // Initialize particles on load
            createParticles();

            // Enhanced RSVP form animation
            document.querySelectorAll('input[name="status"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const details = document.getElementById('attendee-details');
                    if (this.value === 'confirmed') {
                        details.classList.remove('hidden');
                        details.style.animation = 'slideInUp 0.5s ease-out';
                    } else {
                        details.style.animation = 'slideOutDown 0.5s ease-out';
                        setTimeout(() => details.classList.add('hidden'), 500);
                    }
                });
            });

            // Add slide animations CSS
            const animationStyle = document.createElement('style');
            animationStyle.textContent = `
        @keyframes slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutDown {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(20px);
                opacity: 0;
            }
        }
    `;
            document.head.appendChild(animationStyle);

            // Enhanced Confetti Animation
            function createConfetti() {
                const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffa07a', '#dda0dd', '#98fb98', '#f4a460', '#ffb6c1', '#90ee90'];
                const shapes = ['●', '■', '▲', '★', '♥', '🎉', '🎊', '🦕', '🎂', '🎈'];
                const confettiCount = 200;

                for (let i = 0; i < confettiCount; i++) {
                    setTimeout(() => {
                        const confetti = document.createElement('div');
                        const isShape = Math.random() > 0.5;

                        confetti.style.position = 'fixed';
                        confetti.style.left = Math.random() * 100 + 'vw';
                        confetti.style.top = '-40px';
                        confetti.style.pointerEvents = 'none';
                        confetti.style.zIndex = '10000';
                        confetti.style.userSelect = 'none';

                        if (isShape) {
                            confetti.innerHTML = shapes[Math.floor(Math.random() * shapes.length)];
                            confetti.style.fontSize = Math.random() * 25 + 20 + 'px';
                            confetti.style.color = colors[Math.floor(Math.random() * colors.length)];
                            confetti.style.textShadow = '2px 2px 4px rgba(0,0,0,0.2)';
                        } else {
                            confetti.style.width = Math.random() * 10 + 5 + 'px';
                            confetti.style.height = Math.random() * 10 + 5 + 'px';
                            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                            confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                            confetti.style.boxShadow = '0 0 10px rgba(255,255,255,0.5)';
                        }

                        const duration = Math.random() * 3 + 2;
                        const swayAmount = Math.random() * 100 - 50;
                        confetti.style.animation = `confetti-fall ${duration}s ease-out forwards`;
                        confetti.style.setProperty('--sway-amount', swayAmount + 'px');

                        document.body.appendChild(confetti);

                        setTimeout(() => {
                            if (confetti.parentNode) {
                                confetti.remove();
                            }
                        }, duration * 1000 + 500);
                    }, i * 10);
                }
            }

            // Enhanced confetti animation with sway
            const confettiStyle = document.createElement('style');
            confettiStyle.textContent = `
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) translateX(0) rotate(0deg) scale(1);
                opacity: 1;
            }
            25% {
                transform: translateY(25vh) translateX(var(--sway-amount)) rotate(180deg) scale(1.1);
                opacity: 1;
            }
            50% {
                transform: translateY(50vh) translateX(calc(var(--sway-amount) * -0.5)) rotate(360deg) scale(0.9);
                opacity: 1;
            }
            75% {
                transform: translateY(75vh) translateX(calc(var(--sway-amount) * 0.75)) rotate(540deg) scale(0.8);
                opacity: 0.8;
            }
            100% {
                transform: translateY(120vh) translateX(var(--sway-amount)) rotate(720deg) scale(0.6);
                opacity: 0;
            }
        }
    `;
            document.head.appendChild(confettiStyle);

            // Enhanced celebration message
            function showCelebrationMessage(message) {
                const celebrationDiv = document.createElement('div');
                celebrationDiv.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-green-400 via-emerald-500 to-green-600 text-white px-12 py-6 rounded-full text-2xl md:text-3xl font-bold shadow-2xl z-50';
                celebrationDiv.style.animation = 'celebration-pop 0.6s ease-out';
                celebrationDiv.innerHTML = `<span class="flex items-center gap-3">${message}</span>`;

                document.body.appendChild(celebrationDiv);

                // Add pop animation
                const popStyle = document.createElement('style');
                popStyle.textContent = `
            @keyframes celebration-pop {
                0% {
                    transform: translate(-50%, -50%) scale(0) rotate(-180deg);
                    opacity: 0;
                }
                50% {
                    transform: translate(-50%, -50%) scale(1.2) rotate(10deg);
                }
                100% {
                    transform: translate(-50%, -50%) scale(1) rotate(0deg);
                    opacity: 1;
                }
            }
        `;
                document.head.appendChild(popStyle);

                setTimeout(() => {
                    celebrationDiv.style.animation = 'celebration-pop 0.6s ease-out reverse';
                    setTimeout(() => {
                        celebrationDiv.remove();
                    }, 600);
                }, 3000);
            }

            // Enhanced Message Form Handler
            document.getElementById('message-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const formData = new FormData(form);
                const submitButton = document.getElementById('submit-message');
                const feedback = document.getElementById('message-feedback');
                const originalButtonText = submitButton.innerHTML;

                // Disable submit button and show loading with animation
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="flex items-center justify-center gap-2"><span class="loading-dot"></span><span class="loading-dot"></span><span class="loading-dot"></span> Versturen...</span>';
                feedback.classList.add('hidden');

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message with animation
                            feedback.className = 'bg-gradient-to-r from-green-100 to-green-200 border-2 border-green-400 text-green-700 px-6 py-4 rounded-lg mt-4 font-bold shadow-lg';
                            feedback.innerHTML = '<span class="flex items-center gap-2">✅ ' + data.message + '</span>';
                            feedback.classList.remove('hidden');
                            feedback.style.animation = 'slideInUp 0.5s ease-out';

                            // Reset form
                            form.reset();

                            // Hide audio preview if visible
                            const audioPreview = document.getElementById('audio-preview');
                            audioPreview.classList.add('hidden');

                            // Trigger enhanced confetti celebration
                            createConfetti();

                            // Show celebration message
                            showCelebrationMessage('🎉 Bericht verstuurd! Bedankt! 💌');

                        } else {
                            // Show error message
                            feedback.className = 'bg-gradient-to-r from-red-100 to-red-200 border-2 border-red-400 text-red-700 px-6 py-4 rounded-lg mt-4 font-bold shadow-lg';
                            feedback.innerHTML = '<span class="flex items-center gap-2">❌ ' + (data.message || 'Er ging iets mis. Probeer het opnieuw.') + '</span>';
                            feedback.classList.remove('hidden');
                            feedback.style.animation = 'shake 0.5s ease-out';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        feedback.className = 'bg-gradient-to-r from-red-100 to-red-200 border-2 border-red-400 text-red-700 px-6 py-4 rounded-lg mt-4 font-bold shadow-lg';
                        feedback.innerHTML = '<span class="flex items-center gap-2">❌ Er ging iets mis. Probeer het opnieuw.</span>';
                        feedback.classList.remove('hidden');
                        feedback.style.animation = 'shake 0.5s ease-out';
                    })
                    .finally(() => {
                        // Re-enable submit button
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonText;

                        // Auto-hide feedback after 5 seconds
                        setTimeout(() => {
                            feedback.style.animation = 'slideOutDown 0.5s ease-out';
                            setTimeout(() => feedback.classList.add('hidden'), 500);
                        }, 5000);
                    });
            });

            // Add shake animation for errors
            const shakeStyle = document.createElement('style');
            shakeStyle.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
    `;
            document.head.appendChild(shakeStyle);

            // Enhanced RSVP form submission
            document.querySelector('form[action*="rsvp"]').addEventListener('submit', function(e) {
                const confirmedRadio = document.querySelector('input[name="status"][value="confirmed"]');
                if (confirmedRadio && confirmedRadio.checked) {
                    e.preventDefault();

                    // Create extra special confetti for RSVP
                    createConfetti();

                    // Add party sound effect (optional - you can add an actual sound file)
                    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZURE');
                    audio.volume = 0.3;
                    audio.play().catch(() => {}); // Ignore if audio doesn't play

                    // Show mega celebration
                    showCelebrationMessage('🎊 HOERA! Je komt naar het feest! 🎉');

                    // Submit form after celebration
                    setTimeout(() => {
                        this.submit();
                    }, 4000);
                }
            });

            // Enhanced Audio Recording with visual feedback
            let mediaRecorder;
            let recordedChunks = [];
            let recordingTimer;
            let recordingStartTime;

            const recordButton = document.getElementById('record-button');
            const recordingStatus = document.getElementById('recording-status');
            const recordingTime = document.getElementById('recording-time');
            const audioPreview = document.getElementById('audio-preview');
            const audioFileInput = document.getElementById('audio-file-input');

            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                recordButton.addEventListener('click', toggleRecording);
            } else {
                recordButton.style.display = 'none';
                audioFileInput.classList.remove('hidden');
            }

            async function toggleRecording() {
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    stopRecording();
                } else {
                    startRecording();
                }
            }

            async function startRecording() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: {
                            echoCancellation: true,
                            noiseSuppression: true,
                            sampleRate: 44100
                        }
                    });

                    recordedChunks = [];
                    mediaRecorder = new MediaRecorder(stream, {
                        mimeType: MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                            ? 'audio/webm;codecs=opus'
                            : 'audio/webm'
                    });

                    mediaRecorder.ondataavailable = (event) => {
                        if (event.data.size > 0) {
                            recordedChunks.push(event.data);
                        }
                    };

                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(recordedChunks, {
                            type: mediaRecorder.mimeType || 'audio/webm'
                        });

                        const audioUrl = URL.createObjectURL(audioBlob);
                        audioPreview.src = audioUrl;
                        audioPreview.classList.remove('hidden');
                        audioPreview.style.animation = 'slideInUp 0.5s ease-out';

                        const audioFile = new File([audioBlob], 'recording.webm', {
                            type: audioBlob.type
                        });

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(audioFile);
                        audioFileInput.files = dataTransfer.files;

                        stream.getTracks().forEach(track => track.stop());
                    };

                    mediaRecorder.start(1000);
                    recordingStartTime = Date.now();

                    // Update button UI
                    recordButton.innerHTML = '⏹️ Stop Recording';
                    recordButton.classList.remove('bg-red-500', 'hover:bg-red-600');
                    recordButton.classList.add('bg-gray-500', 'hover:bg-gray-600', 'animate-pulse');
                    recordingStatus.classList.remove('hidden');
                    recordingStatus.style.animation = 'slideInUp 0.3s ease-out';

                    recordingTimer = setInterval(updateRecordingTime, 100);

                } catch (error) {
                    console.error('Error starting recording:', error);
                    alert('Could not start recording. Please check your microphone permissions.');
                    recordButton.style.display = 'none';
                    audioFileInput.classList.remove('hidden');
                }
            }

            function stopRecording() {
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                    clearInterval(recordingTimer);

                    recordButton.innerHTML = '🎤 Start Recording';
                    recordButton.classList.remove('bg-gray-500', 'hover:bg-gray-600', 'animate-pulse');
                    recordButton.classList.add('bg-red-500', 'hover:bg-red-600');

                    recordingStatus.style.animation = 'slideOutDown 0.3s ease-out';
                    setTimeout(() => recordingStatus.classList.add('hidden'), 300);
                }
            }

            function updateRecordingTime() {
                if (recordingStartTime) {
                    const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                    const minutes = Math.floor(elapsed / 60);
                    const seconds = elapsed % 60;
                    recordingTime.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                    // Add visual pulse to recording dots
                    document.querySelectorAll('.loading-dot').forEach((dot, index) => {
                        dot.style.transform = `scale(${1 + Math.sin((elapsed + index) * 2) * 0.3})`;
                    });
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Add entrance animations to cards
                const cards = document.querySelectorAll('.hover-lift');
                cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(30px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.6s ease-out';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            });
        </script>
    @endpush
@endsection
