@extends('layouts.app')

@section('title', __('messages.welcome') . " {$guest->name}!")

@section('content')
@php
    // Set locale based on guest's preferred language
    if ($guest->preferred_language) {
        app()->setLocale($guest->preferred_language);
    }
@endphp
<div class="container mx-auto px-4 py-8">
    <!-- Language Switcher -->
    <div class="fixed top-4 right-4 z-50">
        <form action="{{ route('guest.switch-language', $guest->unique_url) }}" method="POST" class="flex items-center space-x-2">
            @csrf
            <button type="submit" name="language" value="{{ $guest->preferred_language == 'nl' ? 'en' : 'nl' }}"
                    class="bg-white bg-opacity-95 hover:bg-opacity-100 shadow-lg rounded-full px-3 py-2 md:px-4 md:py-2 flex items-center space-x-2 transition-all hover:scale-105 border border-gray-200">
                @if($guest->preferred_language == 'nl')
                    <span class="text-lg md:text-xl">🇬🇧</span>
                    <span class="font-medium text-gray-700 text-sm md:text-base">English</span>
                @else
                    <span class="text-lg md:text-xl">🇳🇱</span>
                    <span class="font-medium text-gray-700 text-sm md:text-base">Nederlands</span>
                @endif
            </button>
        </form>
    </div>

    <!-- Personalized Welcome with Hero Background -->
    <div class="relative mb-8 rounded-3xl overflow-hidden shadow-xl {{ $guest->friendship_photo_path ? '' : 'bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400' }}"
         @if($guest->friendship_photo_path)
         style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('{{ Storage::url($guest->friendship_photo_path) }}');
                background-size: cover;
                background-position: center;
                min-height: 400px;"
         @else
         style="min-height: 300px;"
         @endif>

        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="relative z-10 flex items-center justify-center h-full py-16 px-8 text-center">
            <div>
                <h1 class="text-4xl md:text-6xl fun-font text-white mb-4 drop-shadow-lg">
                    {{ __('messages.hello') }} {{ $guest->name }}! 👋
                </h1>
                <p class="text-xl md:text-2xl text-white drop-shadow-lg">
                    {{ __('messages.invitation_sentence', ['age' => $partyDetails->child_age]) }}
                </p>

                @if(!$guest->friendship_photo_path)
                <div class="mt-6 text-6xl">
                    🎉 🎂 🎈 🎁 🦕
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Party Details Card -->
    <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6 mb-8">
        <!-- Left Column - Party Info -->
        <div class="bg-white rounded-3xl shadow-xl p-6">
            <h2 class="text-3xl fun-font text-pink-500 mb-4">{{ __('messages.party_details') }} 🎉</h2>

            <div class="space-y-4">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">📅</span>
                    <div>
                        <p class="font-bold">{{ __('messages.date') }}</p>
                        <p>{{ $partyDetails->getFormattedDate() }}</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <span class="text-2xl mr-3">⏰</span>
                    <div>
                        <p class="font-bold">{{ __('messages.time') }}</p>
                        <p>{{ $partyDetails->getFormattedTime() }}</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <span class="text-2xl mr-3">📍</span>
                    <div>
                        <p class="font-bold">{{ __('messages.location') }}</p>
                        <p>{{ $partyDetails->venue_name }}</p>
                        <p class="text-sm text-gray-600">{{ $partyDetails->venue_address }}</p>
                        @if($partyDetails->venue_map_url)
                            <a href="{{ $partyDetails->venue_map_url }}" target="_blank" class="text-blue-500 hover:underline text-sm">
                                {{ __('messages.view_on_map') }} →
                            </a>
                        @endif
                    </div>
                </div>

                @if($partyDetails->theme)
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🦕</span>
                    <div>
                        <p class="font-bold">{{ __('messages.theme') }}</p>
                        <p>{{ $partyDetails->theme }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column - RSVP -->
        <div class="bg-gradient-to-br from-purple-100 to-pink-100 rounded-3xl shadow-xl p-6">
            <h2 class="text-3xl fun-font text-purple-600 mb-4">{{ __('messages.rsvp') }} 📮</h2>

            @if($guest->rsvp)
                <div class="bg-white rounded-2xl p-4 mb-4">
                    <p class="text-lg mb-2">{{ __('messages.current_status') }}:
                        <span class="font-bold {{ $guest->rsvp->status == 'confirmed' ? 'text-green-600' : ($guest->rsvp->status == 'declined' ? 'text-red-600' : 'text-yellow-600') }}">
                            @if($guest->rsvp->status == 'confirmed')
                                {{ __('messages.confirmed') }}
                            @elseif($guest->rsvp->status == 'declined')
                                {{ __('messages.declined') }}
                            @else
                                {{ __('messages.pending') }}
                            @endif
                        </span>
                    </p>
                    @if($guest->rsvp->status == 'confirmed')
                        <p>{{ __('messages.attending') }}: {{ $guest->rsvp->adults_attending }} {{ __('messages.adults') }}, {{ $guest->rsvp->children_attending }} {{ __('messages.children') }}</p>
                    @endif
                </div>
            @endif

            <form action="{{ route('rsvp.store', $guest) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-lg font-bold mb-2">{{ __('messages.will_you_attend') }}</label>
                    <div class="space-y-2">
                        <label class="flex items-center bg-white rounded-lg p-3 hover:bg-green-50 cursor-pointer">
                            <input type="radio" name="status" value="confirmed" class="mr-2"
                                {{ $guest->rsvp && $guest->rsvp->status == 'confirmed' ? 'checked' : '' }}>
                            <span>{{ __('messages.yes_attending') }}</span>
                        </label>
                        <label class="flex items-center bg-white rounded-lg p-3 hover:bg-red-50 cursor-pointer">
                            <input type="radio" name="status" value="declined" class="mr-2"
                                {{ $guest->rsvp && $guest->rsvp->status == 'declined' ? 'checked' : '' }}>
                            <span>{{ __('messages.no_attending') }}</span>
                        </label>
                    </div>
                </div>

                <div id="attendee-details" class="{{ !$guest->rsvp || $guest->rsvp->status != 'confirmed' ? 'hidden' : '' }}">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold mb-1">{{ __('messages.adults') }}</label>
                            <input type="number" name="adults_attending" min="0" max="10" value="{{ $guest->rsvp ? $guest->rsvp->adults_attending : 1 }}"
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">{{ __('messages.children') }}</label>
                            <input type="number" name="children_attending" min="0" max="10" value="{{ $guest->rsvp ? $guest->rsvp->children_attending : 1 }}"
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1">{{ __('messages.dietary_restrictions') }}</label>
                        <textarea name="dietary_restrictions" rows="2"
                            class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                            placeholder="{{ __('messages.dietary_placeholder') }}">{{ $guest->rsvp ? $guest->rsvp->dietary_restrictions : '' }}</textarea>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-3 px-6 rounded-full hover:shadow-lg transform hover:scale-105 transition-all">
                    {{ $guest->rsvp ? __('messages.update_rsvp') : __('messages.submit_rsvp') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Message/Guestbook Section -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-xl p-6 mb-8">
        <h2 class="text-3xl fun-font text-green-500 mb-4">{{ __('messages.leave_message') }} 💌</h2>

        @if($guest->messages->count() > 0)
            <div class="bg-green-100 rounded-lg p-4 mb-4">
                <p class="text-green-700">{{ __('messages.message_success') }} {{ $partyDetails->child_name }} will love reading it!</p>
            </div>
        @endif

        <form id="message-form" action="{{ route('message.store', $guest) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-lg font-bold mb-2">{{ __('messages.your_message') }}</label>
                <textarea name="message" rows="4" required
                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                    placeholder="{{ __('messages.message_placeholder', ['name' => $partyDetails->child_name]) }}"></textarea>
            </div>

            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-1">{{ __('messages.upload_drawing') }}</label>
                    <input type="file" name="drawing" accept="image/*" class="text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">{{ __('messages.upload_photo') }}</label>
                    <input type="file" name="photo" accept="image/*" class="text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">{{ __('messages.record_audio') }}</label>
                    <div id="audio-recorder" class="space-y-2">
                        <button type="button" id="record-button" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold">
                            🎤 Start Recording
                        </button>
                        <div id="recording-status" class="text-sm text-gray-600 hidden">
                            Recording... <span id="recording-time">0:00</span>
                        </div>
                        <audio id="audio-preview" controls class="w-full hidden"></audio>
                        <input type="file" id="audio-file-input" name="audio" accept="audio/*" class="text-sm hidden">
                    </div>
                </div>
            </div>

            <button type="submit" id="submit-message" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-full transform hover:scale-105 transition-all">
                {{ __('messages.send_message') }} 🎉
            </button>

            <!-- Success/Error Messages -->
            <div id="message-feedback" class="hidden"></div>
        </form>
    </div>

    <!-- Gift Registry Section -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="bg-gradient-to-r from-orange-100 to-red-100 rounded-3xl shadow-xl p-6 text-center">
            <h3 class="text-2xl fun-font text-orange-600 mb-3">🎁 {{ __('messages.gift_registry') }} 🎁</h3>
            <p class="text-orange-700 mb-4">
                Want to bring a gift? Check out Liam's birthday wishlist to see what he'd love most!
                You can claim a gift to avoid duplicates.
            </p>
            <a href="{{ route('gifts.index') }}"
               class="inline-block bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold py-3 px-6 rounded-full hover:shadow-lg transform hover:scale-105 transition-all">
                👀 View Wishlist
            </a>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="max-w-4xl mx-auto grid md:grid-cols-2 gap-6">
        @if($partyDetails->activities)
        <div class="bg-yellow-50 rounded-3xl shadow-xl p-6">
            <h3 class="text-2xl fun-font text-yellow-600 mb-3">{{ __('messages.activities') }} 🎪</h3>
            <p>{{ $partyDetails->activities }}</p>
        </div>
        @endif

        @if($partyDetails->gift_suggestions)
        <div class="bg-blue-50 rounded-3xl shadow-xl p-6">
            <h3 class="text-2xl fun-font text-blue-600 mb-3">{{ __('messages.gift_ideas') }} 🎁</h3>
            <p>{{ $partyDetails->gift_suggestions }}</p>
        </div>
        @endif
    </div>

    <!-- Contact Info -->
    <div class="text-center mt-8 text-gray-600">
        <p>{{ __('messages.questions') }} {{ __('messages.contact') }}: {{ $partyDetails->parent_contact_info }}</p>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide attendee details based on RSVP status
    document.querySelectorAll('input[name="status"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const details = document.getElementById('attendee-details');
            if (this.value === 'confirmed') {
                details.classList.remove('hidden');
            } else {
                details.classList.add('hidden');
            }
        });
    });

    // Helper function to trigger confetti
    function triggerConfetti() {
        createConfetti();
    }

    // Helper function to show celebration message
    function showCelebrationMessage(message) {
        const celebrationDiv = document.createElement('div');
        celebrationDiv.className = 'celebration-message fixed top-1/2 left-1/2 bg-gradient-to-r from-green-400 to-green-600 text-white px-8 py-4 rounded-full text-xl md:text-2xl font-bold shadow-lg z-50';
        celebrationDiv.innerHTML = message;
        document.body.appendChild(celebrationDiv);

        setTimeout(() => {
            celebrationDiv.style.animation = 'celebration-bounce 0.6s ease-out reverse';
            setTimeout(() => {
                celebrationDiv.remove();
            }, 600);
        }, 3000);
    }

    // Confetti Animation Function
    function createConfetti() {
        const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffa07a', '#dda0dd', '#98fb98', '#f4a460', '#ffb6c1', '#90ee90'];
        const shapes = ['●', '■', '▲', '★', '♥', '🎉', '🎊'];
        const confettiCount = 150;

        for (let i = 0; i < confettiCount; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                const isShape = Math.random() > 0.7;

                confetti.style.position = 'fixed';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = '-20px';
                confetti.style.pointerEvents = 'none';
                confetti.style.zIndex = '10000';
                confetti.style.userSelect = 'none';

                if (isShape) {
                    confetti.innerHTML = shapes[Math.floor(Math.random() * shapes.length)];
                    confetti.style.fontSize = Math.random() * 20 + 15 + 'px';
                    confetti.style.color = colors[Math.floor(Math.random() * colors.length)];
                } else {
                    confetti.style.width = Math.random() * 8 + 5 + 'px';
                    confetti.style.height = Math.random() * 8 + 5 + 'px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                }

                const duration = Math.random() * 3 + 2;
                confetti.style.animation = `confetti-fall ${duration}s linear forwards`;

                document.body.appendChild(confetti);

                setTimeout(() => {
                    if (confetti.parentNode) {
                        confetti.remove();
                    }
                }, duration * 1000 + 500);
            }, i * 15);
        }
    }

    // Add confetti CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg) scale(1);
                opacity: 1;
            }
            50% {
                opacity: 1;
                transform: translateY(50vh) rotate(360deg) scale(0.8);
            }
            100% {
                transform: translateY(120vh) rotate(720deg) scale(0.5);
                opacity: 0;
            }
        }
        .celebration-message {
            animation: celebration-bounce 0.6s ease-out;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        @keyframes celebration-bounce {
            0% { transform: translate(-50%, -50%) scale(0.3); opacity: 0; }
            50% { transform: translate(-50%, -50%) scale(1.1); }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    // Handle Message Form with AJAX
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const submitButton = document.getElementById('submit-message');
        const feedback = document.getElementById('message-feedback');

        // Disable submit button and show loading
        submitButton.disabled = true;
        submitButton.innerHTML = '📤 Versturen...';
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
                // Show success message
                feedback.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4';
                feedback.innerHTML = '✅ ' + data.message;
                feedback.classList.remove('hidden');

                // Reset form
                form.reset();

                // Trigger confetti celebration
                triggerConfetti();

                // Show celebration message
                showCelebrationMessage('🎉 Bericht verstuurd! Bedankt! 💌');

            } else {
                // Show error message
                feedback.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4';
                feedback.innerHTML = '❌ ' + (data.message || 'Er ging iets mis. Probeer het opnieuw.');
                feedback.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            feedback.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4';
            feedback.innerHTML = '❌ Er ging iets mis. Probeer het opnieuw.';
            feedback.classList.remove('hidden');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = '{{ __("messages.send_message") }} 🎉';

            // Auto-hide feedback after 5 seconds
            setTimeout(() => {
                feedback.classList.add('hidden');
            }, 5000);
        });
    });

    // Trigger confetti on RSVP yes
    document.querySelector('form[action*="rsvp"]').addEventListener('submit', function(e) {
        const confirmedRadio = document.querySelector('input[name="status"][value="confirmed"]');
        if (confirmedRadio && confirmedRadio.checked) {
            e.preventDefault(); // Prevent form submission briefly to show animation

            createConfetti();

            // Add celebration message with better styling
            const celebrationDiv = document.createElement('div');
            celebrationDiv.className = 'celebration-message fixed top-1/2 left-1/2 bg-gradient-to-r from-green-400 to-green-600 text-white px-8 py-4 rounded-full text-xl md:text-2xl font-bold shadow-lg';
            celebrationDiv.style.zIndex = '10001';
            celebrationDiv.innerHTML = '{{ __("messages.celebration_message") }}';
            document.body.appendChild(celebrationDiv);

            // Submit form after celebration
            setTimeout(() => {
                celebrationDiv.style.animation = 'celebration-bounce 0.6s ease-out reverse';
                setTimeout(() => {
                    celebrationDiv.remove();
                    this.submit(); // Now submit the form
                }, 600);
            }, 4000); // Show for 4 seconds
        }
    });

    // Audio Recording Implementation
    let mediaRecorder;
    let recordedChunks = [];
    let recordingTimer;
    let recordingStartTime;

    const recordButton = document.getElementById('record-button');
    const recordingStatus = document.getElementById('recording-status');
    const recordingTime = document.getElementById('recording-time');
    const audioPreview = document.getElementById('audio-preview');
    const audioFileInput = document.getElementById('audio-file-input');

    // Check if MediaRecorder is supported
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        recordButton.addEventListener('click', toggleRecording);
    } else {
        // Fallback to file input for unsupported browsers
        recordButton.style.display = 'none';
        audioFileInput.classList.remove('hidden');
        audioFileInput.classList.add('text-sm');
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

                // Create audio URL for preview
                const audioUrl = URL.createObjectURL(audioBlob);
                audioPreview.src = audioUrl;
                audioPreview.classList.remove('hidden');

                // Create file for form submission
                const audioFile = new File([audioBlob], 'recording.webm', {
                    type: audioBlob.type
                });

                // Use DataTransfer to set file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(audioFile);
                audioFileInput.files = dataTransfer.files;

                // Stop all tracks
                stream.getTracks().forEach(track => track.stop());
            };

            mediaRecorder.start(1000); // Collect data every second
            recordingStartTime = Date.now();

            // Update UI
            recordButton.textContent = '⏹️ Stop Recording';
            recordButton.classList.remove('bg-red-500', 'hover:bg-red-600');
            recordButton.classList.add('bg-gray-500', 'hover:bg-gray-600');
            recordingStatus.classList.remove('hidden');

            // Start timer
            recordingTimer = setInterval(updateRecordingTime, 1000);

        } catch (error) {
            console.error('Error starting recording:', error);
            alert('Could not start recording. Please check your microphone permissions or use the file upload option.');

            // Show file input as fallback
            recordButton.style.display = 'none';
            audioFileInput.classList.remove('hidden');
            audioFileInput.classList.add('text-sm');
        }
    }

    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop();
            clearInterval(recordingTimer);

            // Update UI
            recordButton.textContent = '🎤 Start Recording';
            recordButton.classList.remove('bg-gray-500', 'hover:bg-gray-600');
            recordButton.classList.add('bg-red-500', 'hover:bg-red-600');
            recordingStatus.classList.add('hidden');
        }
    }

    function updateRecordingTime() {
        if (recordingStartTime) {
            const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            recordingTime.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    }
</script>
@endpush
@endsection
