@extends('layouts.app')

@section('title', 'Manage Guests')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl fun-font text-purple-600 mb-2">Manage Guests</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Back to Dashboard</a>
    </div>

    <!-- CSV Import Section -->
    <div class="bg-blue-50 rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-blue-800 mb-4">📤 Bulk Import Guests</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-bold text-gray-700 mb-2">Upload CSV File</h3>
                <form action="{{ route('admin.guests.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <input type="file" name="csv_file" accept=".csv,.txt" required
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-600 mt-1">Upload a CSV file with guest information</p>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full">
                        🚀 Import Guests
                    </button>
                </form>
            </div>
            
            <div>
                <h3 class="font-bold text-gray-700 mb-2">Download Sample</h3>
                <p class="text-gray-600 mb-3">Need a template? Download our sample CSV file to see the correct format.</p>
                <a href="{{ route('admin.guests.sample-csv') }}" 
                   class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full">
                    📥 Download Sample CSV
                </a>
                
                <div class="mt-4 text-xs text-gray-600">
                    <strong>Required CSV columns:</strong>
                    <ul class="list-disc list-inside mt-1">
                        <li><strong>name</strong> (required) - Full name of the guest</li>
                        <li><strong>email</strong> (optional) - Email for receipts/notifications</li>
                        <li><strong>phone</strong> (optional) - Contact phone number</li>
                        <li><strong>date_of_birth</strong> (optional) - Format: YYYY-MM-DD (e.g., 2019-03-15)</li>
                        <li><strong>preferred_language</strong> (optional) - 'en' or 'nl' for translations</li>
                    </ul>
                    <p class="mt-2 text-gray-500">Only 'name' is required. All other fields can be empty.</p>
                </div>
            </div>
        </div>
        
        @if(session('error'))
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Add New Guest Form -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Add Single Guest</h2>
        
        <form action="{{ route('admin.guests.store') }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guest Name *</label>
                <input type="text" name="name" required 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Is Child?</label>
                <select name="is_child" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">Used for age calculation and age-restricted services</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Name (English)</label>
                <input type="text" name="parent_name" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       placeholder="e.g., Mom, Dad">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Name (Dutch)</label>
                <input type="text" name="parent_name_nl" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       placeholder="e.g., Mama, Papa">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Email (English)</label>
                <input type="email" name="parent_email" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Email (Dutch)</label>
                <input type="email" name="parent_email_nl" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Phone (English)</label>
                <input type="text" name="parent_phone" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Phone (Dutch)</label>
                <input type="text" name="parent_phone_nl" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Language</label>
                <select name="preferred_language" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="en">English</option>
                    <option value="nl">Dutch (Nederlands)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expected Attendees</label>
                <input type="number" name="expected_attendees" min="1" max="10" value="1"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Friendship Photo (Liam + Guest)</label>
                <input type="file" name="friendship_photo" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-xs text-gray-500 mt-1">Upload a photo of Liam and this guest together (optional)</p>
            </div>
            
            <div class="md:col-span-2">
                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-full">
                    Add Guest
                </button>
            </div>
        </form>
    </div>

    <!-- Guests List -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Guest List ({{ $guests->count() }})</h2>
            <div class="space-x-2">
                <a href="{{ route('admin.guests.export') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    📥 Export to CSV
                </a>
                <form action="{{ route('admin.guests.generate-all-qr') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm">
                        📱 Generate All QR Codes
                    </button>
                </form>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RSVP Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invitation Link</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($guests as $guest)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="relative group">
                                @if($guest->friendship_photo_path)
                                    <img src="{{ Storage::url($guest->friendship_photo_path) }}" 
                                         alt="Photo of {{ $guest->name }} and Liam"
                                         class="w-16 h-16 rounded-lg object-cover cursor-pointer hover:opacity-80"
                                         onclick="window.open(this.src)">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                                        📷
                                    </div>
                                @endif
                                
                                <!-- Upload button overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                    <button onclick="openPhotoUpload({{ $guest->id }}, '{{ $guest->name }}')" 
                                            class="text-white text-xs font-bold px-2 py-1 rounded bg-blue-600 hover:bg-blue-700">
                                        {{ $guest->friendship_photo_path ? 'Change' : 'Upload' }}
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $guest->name }}</div>
                            @if($guest->email)
                                <div class="text-sm text-gray-500">{{ $guest->email }}</div>
                            @endif
                            @if($guest->phone)
                                <div class="text-sm text-gray-500">{{ $guest->phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($guest->date_of_birth)
                                <div>{{ $guest->getAge() }} years</div>
                                <div class="text-xs text-gray-400">{{ $guest->getFormattedDateOfBirth() }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $guest->preferred_language == 'nl' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $guest->preferred_language == 'nl' ? '🇳🇱 Dutch' : '🇬🇧 English' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($guest->rsvp)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $guest->rsvp->status == 'confirmed' ? 'bg-green-100 text-green-800' : ($guest->rsvp->status == 'declined' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($guest->rsvp->status) }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    No Response
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <img src="{{ $guest->getQrCodeUrl() }}" alt="QR Code for {{ $guest->name }}" 
                                     class="w-12 h-12 cursor-pointer hover:opacity-80" 
                                     onclick="window.open(this.src)">
                                <a href="{{ $guest->getQrCodeUrl() }}" download="qr-{{ $guest->slug }}.png" 
                                   class="text-purple-600 hover:text-purple-800 text-xs">
                                    📥 Download
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <input type="text" value="{{ url('/invite/' . $guest->unique_url) }}" 
                                       class="text-xs bg-gray-50 px-2 py-1 rounded w-64" readonly>
                                <button onclick="navigator.clipboard.writeText('{{ url('/invite/' . $guest->unique_url) }}')" 
                                        class="text-purple-600 hover:text-purple-800">
                                    📋
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Photo Upload Modal -->
<div id="photo-upload-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4" id="modal-title">Upload Friendship Photo</h3>
            
            <form id="photo-upload-form" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Photo (Liam + Guest)
                    </label>
                    <input type="file" name="friendship_photo" accept="image/*" required
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Max file size: 5MB</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full text-sm">
                        Upload Photo
                    </button>
                    <button type="button" onclick="closePhotoUpload()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-full text-sm">
                        Cancel
                    </button>
                </div>
                
                <!-- Status message -->
                <div id="upload-status" class="mt-3 hidden"></div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentGuestId = null;

    // Photo upload modal functions
    function openPhotoUpload(guestId, guestName) {
        currentGuestId = guestId;
        document.getElementById('modal-title').textContent = `Upload Friendship Photo for ${guestName}`;
        document.getElementById('photo-upload-modal').classList.remove('hidden');
        document.getElementById('upload-status').classList.add('hidden');
        document.getElementById('photo-upload-form').reset();
    }

    function closePhotoUpload() {
        document.getElementById('photo-upload-modal').classList.add('hidden');
        currentGuestId = null;
    }

    // Handle form submission
    document.getElementById('photo-upload-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!currentGuestId) return;
        
        const formData = new FormData(this);
        const statusDiv = document.getElementById('upload-status');
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;
        
        // Update UI
        submitButton.disabled = true;
        submitButton.textContent = 'Uploading...';
        statusDiv.classList.add('hidden');
        
        // Upload photo
        fetch(`/admin/guests/${currentGuestId}/upload-photo`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                statusDiv.className = 'mt-3 bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded text-sm';
                statusDiv.textContent = data.message;
                statusDiv.classList.remove('hidden');
                
                // Update the photo in the table
                setTimeout(() => {
                    location.reload(); // Simple refresh to show new photo
                }, 1000);
                
                // Close modal after delay
                setTimeout(() => {
                    closePhotoUpload();
                }, 2000);
            } else {
                // Show error
                statusDiv.className = 'mt-3 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-sm';
                statusDiv.textContent = data.message || 'Upload failed. Please try again.';
                statusDiv.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            statusDiv.className = 'mt-3 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-sm';
            statusDiv.textContent = 'Upload failed. Please try again.';
            statusDiv.classList.remove('hidden');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        });
    });

    // Close modal when clicking outside
    document.getElementById('photo-upload-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePhotoUpload();
        }
    });

    // Show success message when copying URLs
    document.querySelectorAll('button').forEach(btn => {
        if (btn.textContent === '📋') {
            btn.addEventListener('click', function() {
                const originalText = this.textContent;
                this.textContent = '✓';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 1000);
            });
        }
    });
</script>
@endpush
@endsection