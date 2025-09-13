@extends('layouts.app')

@section('title', 'Manage Guests')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl fun-font text-purple-600 mb-2">Manage Guests</h1>
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Back to Dashboard</a>
            <div class="flex gap-4 text-sm">
                <span class="bg-gray-100 px-3 py-1 rounded-full">
                    Total: <strong>{{ $guests->count() }}</strong>
                </span>
                <span class="bg-green-100 px-3 py-1 rounded-full text-green-800">
                    Confirmed: <strong>{{ $guests->filter(fn($g) => $g->rsvp && $g->rsvp->status === 'confirmed')->count() }}</strong>
                </span>
                <span class="bg-yellow-100 px-3 py-1 rounded-full text-yellow-800">
                    Pending: <strong>{{ $guests->filter(fn($g) => !$g->rsvp)->count() }}</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="flex flex-wrap gap-3 mb-6">
        <button onclick="toggleSection('bulk-import')" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
            📤 Bulk Import
        </button>
        <button onclick="toggleSection('add-single')" 
                class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
            ➕ Add Single Guest
        </button>
    </div>

    <!-- CSV Import Section (Initially Hidden) -->
    <div id="bulk-import" class="hidden bg-blue-50 rounded-xl shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-blue-800">📤 Bulk Import Guests</h2>
            <button onclick="toggleSection('bulk-import')" class="text-gray-500 hover:text-gray-700">
                ✕
            </button>
        </div>
        
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

    <!-- Add New Guest Form (Initially Hidden) -->
    <div id="add-single" class="hidden bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Add Single Guest</h2>
            <button type="button" onclick="toggleSection('add-single')" class="text-gray-500 hover:text-gray-700">
                ✕
            </button>
        </div>
        
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
                <button type="button" id="add-photo-btn" 
                        class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-full text-sm mb-2">
                    📷 Choose & Crop Photo
                </button>
                <p class="text-xs text-gray-500 mt-1">Upload a photo of Liam and this guest together (optional)</p>
                
                <!-- Hidden file input -->
                <input type="file" id="add-photo-input" accept="image/*" class="hidden">
                <input type="hidden" name="cropped_photo_data" id="add-cropped-photo-data">
            </div>
            
            <div class="md:col-span-2">
                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-full">
                    Add Guest
                </button>
            </div>
        </form>
    </div>

    <!-- Guests List (Main Section) -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-purple-100">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold text-purple-800 flex items-center gap-2">
                <span>🎉</span> Guest List
            </h2>
            <div class="flex items-center gap-3">
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
        
        @if($guests->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">👥</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No guests yet!</h3>
                <p class="text-gray-500 mb-6">Start by adding guests one at a time or importing from a CSV file.</p>
                <div class="flex justify-center gap-3">
                    <button onclick="toggleSection('add-single')" 
                            class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-full">
                        Add First Guest
                    </button>
                    <button onclick="toggleSection('bulk-import')" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-full">
                        Import CSV
                    </button>
                </div>
            </div>
        @else
        <!-- Quick Search -->
        <div class="px-6 py-3 bg-gray-50 border-b">
            <input type="text" id="guest-search" placeholder="🔍 Search guests by name, email, or phone..." 
                   class="w-full max-w-md px-4 py-2 rounded-lg border border-gray-300 focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('name')">
                            Name <span id="sort-name" class="ml-1">↕️</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('rsvp')">
                            RSVP Status <span id="sort-rsvp" class="ml-1">↕️</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invitation Link</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('age')">
                            Age <span id="sort-age" class="ml-1">↕️</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('language')">
                            Language <span id="sort-language" class="ml-1">↕️</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($guests as $guest)
                    <tr data-name="{{ $guest->name }}" data-rsvp="{{ $guest->rsvp ? $guest->rsvp->status : 'no_response' }}" data-age="{{ $guest->getAge() ?? 0 }}" data-language="{{ $guest->preferred_language }}">
                        <!-- Photo -->
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
                                    @if($guest->friendship_photo_path)
                                        <div class="flex space-x-1">
                                            <button onclick="openPhotoUpload({{ $guest->id }}, '{{ $guest->name }}')" 
                                                    class="text-white text-xs font-bold px-2 py-1 rounded bg-blue-600 hover:bg-blue-700">
                                                New
                                            </button>
                                            <button onclick="openRecropPhoto({{ $guest->id }}, '{{ $guest->name }}', '{{ Storage::url($guest->friendship_photo_path) }}')" 
                                                    class="text-white text-xs font-bold px-2 py-1 rounded bg-green-600 hover:bg-green-700">
                                                Re-crop
                                            </button>
                                        </div>
                                    @else
                                        <button onclick="openPhotoUpload({{ $guest->id }}, '{{ $guest->name }}')" 
                                                class="text-white text-xs font-bold px-2 py-1 rounded bg-blue-600 hover:bg-blue-700">
                                            Upload
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <!-- Name -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $guest->name }}</div>
                            @if($guest->email)
                                <div class="text-sm text-gray-500">{{ $guest->email }}</div>
                            @endif
                            @if($guest->phone)
                                <div class="text-sm text-gray-500">{{ $guest->phone }}</div>
                            @endif
                        </td>
                        
                        <!-- RSVP Status -->
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
                        
                        <!-- Invitation Link -->
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
                        
                        <!-- Age -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($guest->date_of_birth)
                                <div>{{ $guest->getAge() }} years</div>
                                <div class="text-xs text-gray-400">{{ $guest->getFormattedDateOfBirth() }}</div>
                            @else
                                -
                            @endif
                        </td>
                        
                        <!-- Language -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="toggleLanguage({{ $guest->id }}, '{{ $guest->preferred_language }}')"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer hover:opacity-80 transition-opacity
                                        {{ $guest->preferred_language == 'nl' ? 'bg-orange-100 text-orange-800 hover:bg-orange-200' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">
                                {{ $guest->preferred_language == 'nl' ? '🇳🇱 Dutch' : '🇬🇧 English' }}
                            </button>
                        </td>
                        
                        <!-- QR Code -->
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
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <button onclick="editGuest({{ $guest->id }})" 
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                    ✏️ Edit
                                </button>
                                <button onclick="deleteGuest({{ $guest->id }}, '{{ $guest->name }}')" 
                                        class="text-red-600 hover:text-red-800 font-medium">
                                    🗑️ Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Photo Upload Modal -->
<div id="photo-upload-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4" id="modal-title">Upload Friendship Photo</h3>
            
            <!-- Step 1: File Selection -->
            <div id="file-selection-step">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Photo (Liam + Guest)
                    </label>
                    <input type="file" id="photo-file-input" accept="image/*" required
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Max file size: 5MB</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="closePhotoUpload()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-full text-sm">
                        Cancel
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Cropping Interface -->
            <div id="cropping-step" class="hidden">
                <div class="mb-4">
                    <div class="bg-gray-100 rounded-lg p-4 max-h-96 overflow-hidden">
                        <img id="crop-image" class="max-w-full" style="display: block;">
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Drag to move, resize the crop area as needed - any aspect ratio</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" id="upload-cropped-btn"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full text-sm">
                        Upload Cropped Photo
                    </button>
                    <button type="button" onclick="resetCropper()" 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-full text-sm">
                        Choose Different Photo
                    </button>
                    <button type="button" onclick="closePhotoUpload()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-full text-sm">
                        Cancel
                    </button>
                </div>
            </div>
            
            <!-- Status message -->
            <div id="upload-status" class="mt-3 hidden"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentGuestId = null;
    let cropper = null;

    // Photo upload modal functions
    function openPhotoUpload(guestId, guestName) {
        currentGuestId = guestId;
        document.getElementById('modal-title').textContent = `Upload Friendship Photo for ${guestName}`;
        document.getElementById('photo-upload-modal').classList.remove('hidden');
        document.getElementById('upload-status').classList.add('hidden');
        resetToFileSelection();
    }

    function closePhotoUpload() {
        document.getElementById('photo-upload-modal').classList.add('hidden');
        currentGuestId = null;
        resetToFileSelection();
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }
    
    function resetToFileSelection() {
        document.getElementById('file-selection-step').classList.remove('hidden');
        document.getElementById('cropping-step').classList.add('hidden');
        document.getElementById('photo-file-input').value = '';
    }
    
    function resetCropper() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        resetToFileSelection();
    }

    // Handle file selection
    document.getElementById('photo-file-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }
        
        // Show cropping interface
        const reader = new FileReader();
        reader.onload = function(event) {
            const image = document.getElementById('crop-image');
            image.src = event.target.result;
            
            // Hide file selection, show cropping
            document.getElementById('file-selection-step').classList.add('hidden');
            document.getElementById('cropping-step').classList.remove('hidden');
            
            // Initialize cropper
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(image, {
                aspectRatio: NaN, // Free aspect ratio
                viewMode: 1,
                autoCropArea: 0.8,
                movable: true,
                scalable: true,
                rotatable: false,
                zoomable: true,
                minCropBoxWidth: 100,
                minCropBoxHeight: 100
            });
        };
        reader.readAsDataURL(file);
    });
    
    // Handle cropped image upload
    document.getElementById('upload-cropped-btn').addEventListener('click', function() {
        if (!cropper || !currentGuestId) return;
        
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 800,
            maxHeight: 600,
            imageSmoothingQuality: 'high'
        });
        
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('friendship_photo', blob, 'cropped-photo.jpg');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            const statusDiv = document.getElementById('upload-status');
            const uploadButton = document.getElementById('upload-cropped-btn');
            const originalButtonText = uploadButton.textContent;
            
            // Update UI
            uploadButton.disabled = true;
            uploadButton.textContent = 'Uploading...';
            statusDiv.classList.add('hidden');
            
            // Upload cropped photo
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
                uploadButton.disabled = false;
                uploadButton.textContent = originalButtonText;
            });
        }, 'image/jpeg', 0.9);
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

    // Language toggle functionality
    function toggleLanguage(guestId, currentLanguage) {
        const newLanguage = currentLanguage === 'nl' ? 'en' : 'nl';
        
        fetch(`/admin/guests/${guestId}/toggle-language`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ language: newLanguage })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Simple refresh to update the UI
            } else {
                alert('Failed to update language. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update language. Please try again.');
        });
    }

    // Edit guest functionality
    function editGuest(guestId) {
        window.location.href = `/admin/guests/${guestId}/edit`;
    }

    // Delete guest functionality
    function deleteGuest(guestId, guestName) {
        if (confirm(`Are you sure you want to delete ${guestName}? This action cannot be undone.`)) {
            fetch(`/admin/guests/${guestId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Simple refresh to update the UI
                } else {
                    alert('Failed to delete guest. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete guest. Please try again.');
            });
        }
    }

    // Add guest form photo cropping functionality
    let addCropper = null;
    let addCropModal = null;
    
    // Create add guest crop modal
    function createAddCropModal() {
        const modal = document.createElement('div');
        modal.id = 'add-crop-modal';
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-10 mx-auto p-5 border max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Crop Friendship Photo</h3>
                    
                    <div class="mb-4">
                        <div class="bg-gray-100 rounded-lg p-4 max-h-96 overflow-hidden">
                            <img id="add-crop-image" class="max-w-full" style="display: block;">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Drag to move, resize the crop area as needed - any aspect ratio</p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button type="button" id="add-apply-crop-btn"
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full text-sm">
                            Apply Crop
                        </button>
                        <button type="button" id="add-cancel-crop-btn"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-full text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }
    
    // Handle add guest photo selection
    document.getElementById('add-photo-btn').addEventListener('click', function() {
        document.getElementById('add-photo-input').click();
    });
    
    document.getElementById('add-photo-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }
        
        // Create modal if it doesn't exist
        if (!addCropModal) {
            addCropModal = createAddCropModal();
        }
        
        // Show cropping modal
        const reader = new FileReader();
        reader.onload = function(event) {
            const image = document.getElementById('add-crop-image');
            image.src = event.target.result;
            
            // Show modal
            addCropModal.classList.remove('hidden');
            
            // Initialize cropper
            if (addCropper) {
                addCropper.destroy();
            }
            
            addCropper = new Cropper(image, {
                aspectRatio: NaN, // Free aspect ratio
                viewMode: 1,
                autoCropArea: 0.8,
                movable: true,
                scalable: true,
                rotatable: false,
                zoomable: true,
                minCropBoxWidth: 100,
                minCropBoxHeight: 100
            });
        };
        reader.readAsDataURL(file);
    });
    
    // Handle add crop apply button (event delegation)
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'add-apply-crop-btn') {
            if (!addCropper) return;
            
            const canvas = addCropper.getCroppedCanvas({
                maxWidth: 800,
                maxHeight: 600,
                imageSmoothingQuality: 'high'
            });
            
            // Convert to base64 and store in hidden field
            const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('add-cropped-photo-data').value = croppedDataUrl;
            
            // Update button text to show photo is selected
            document.getElementById('add-photo-btn').innerHTML = '✅ Photo Selected';
            document.getElementById('add-photo-btn').classList.remove('bg-purple-500', 'hover:bg-purple-600');
            document.getElementById('add-photo-btn').classList.add('bg-green-500', 'hover:bg-green-600');
            
            // Close modal
            addCropModal.classList.add('hidden');
            if (addCropper) {
                addCropper.destroy();
                addCropper = null;
            }
        }
        
        if (e.target && e.target.id === 'add-cancel-crop-btn') {
            addCropModal.classList.add('hidden');
            document.getElementById('add-photo-input').value = '';
            if (addCropper) {
                addCropper.destroy();
                addCropper = null;
            }
        }
    });
    
    // Re-crop existing photo functionality
    let recropCropper = null;
    let recropModal = null;
    
    // Create re-crop modal
    function createRecropModal() {
        const modal = document.createElement('div');
        modal.id = 'recrop-modal';
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-10 mx-auto p-5 border max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4" id="recrop-title">Re-crop Photo</h3>
                    
                    <div class="mb-4">
                        <div class="bg-gray-100 rounded-lg p-4 max-h-96 overflow-hidden">
                            <img id="recrop-image" class="max-w-full" style="display: block;">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Drag to move, resize the crop area as needed - any aspect ratio</p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button type="button" id="apply-recrop-btn"
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full text-sm">
                            Apply Re-crop
                        </button>
                        <button type="button" id="cancel-recrop-btn"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-full text-sm">
                            Cancel
                        </button>
                    </div>
                    
                    <div id="recrop-status" class="mt-3 hidden"></div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }
    
    // Open re-crop modal
    function openRecropPhoto(guestId, guestName, imageUrl) {
        currentGuestId = guestId;
        
        // Create modal if it doesn't exist
        if (!recropModal) {
            recropModal = createRecropModal();
        }
        
        // Set title and show modal
        document.getElementById('recrop-title').textContent = `Re-crop Photo for ${guestName}`;
        recropModal.classList.remove('hidden');
        
        // Load existing image
        const image = document.getElementById('recrop-image');
        image.src = imageUrl;
        
        // Initialize cropper when image loads
        image.onload = function() {
            if (recropCropper) {
                recropCropper.destroy();
            }
            
            recropCropper = new Cropper(image, {
                aspectRatio: NaN, // Free aspect ratio
                viewMode: 1,
                autoCropArea: 1, // Start with full image selected
                movable: true,
                scalable: true,
                rotatable: false,
                zoomable: true,
                minCropBoxWidth: 100,
                minCropBoxHeight: 100
            });
        };
    }
    
    // Handle re-crop apply button (event delegation)
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'apply-recrop-btn') {
            if (!recropCropper || !currentGuestId) return;
            
            const canvas = recropCropper.getCroppedCanvas({
                maxWidth: 800,
                maxHeight: 600,
                imageSmoothingQuality: 'high'
            });
            
            canvas.toBlob(function(blob) {
                const formData = new FormData();
                formData.append('friendship_photo', blob, 'recropped-photo.jpg');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const statusDiv = document.getElementById('recrop-status');
                const applyButton = document.getElementById('apply-recrop-btn');
                const originalButtonText = applyButton.textContent;
                
                // Update UI
                applyButton.disabled = true;
                applyButton.textContent = 'Re-cropping...';
                statusDiv.classList.add('hidden');
                
                // Upload re-cropped photo
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
                        statusDiv.textContent = 'Photo re-cropped successfully!';
                        statusDiv.classList.remove('hidden');
                        
                        // Update the photo in the table
                        setTimeout(() => {
                            location.reload(); // Simple refresh to show new photo
                        }, 1000);
                        
                        // Close modal after delay
                        setTimeout(() => {
                            recropModal.classList.add('hidden');
                            if (recropCropper) {
                                recropCropper.destroy();
                                recropCropper = null;
                            }
                        }, 2000);
                    } else {
                        // Show error
                        statusDiv.className = 'mt-3 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-sm';
                        statusDiv.textContent = data.message || 'Re-crop failed. Please try again.';
                        statusDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusDiv.className = 'mt-3 bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-sm';
                    statusDiv.textContent = 'Re-crop failed. Please try again.';
                    statusDiv.classList.remove('hidden');
                })
                .finally(() => {
                    applyButton.disabled = false;
                    applyButton.textContent = originalButtonText;
                });
            }, 'image/jpeg', 0.9);
        }
        
        if (e.target && e.target.id === 'cancel-recrop-btn') {
            recropModal.classList.add('hidden');
            if (recropCropper) {
                recropCropper.destroy();
                recropCropper = null;
            }
        }
    });
    
    // Toggle section visibility
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const otherSection = sectionId === 'bulk-import' ? 'add-single' : 'bulk-import';
        
        // Hide the other section
        document.getElementById(otherSection).classList.add('hidden');
        
        // Toggle current section
        section.classList.toggle('hidden');
        
        // Smooth scroll to section if opening
        if (!section.classList.contains('hidden')) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    
    // Guest search functionality
    document.getElementById('guest-search')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const name = row.querySelector('.text-sm.font-medium')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.text-gray-500')?.textContent.toLowerCase() || '';
            const visible = name.includes(searchTerm) || email.includes(searchTerm);
            row.style.display = visible ? '' : 'none';
        });
        
        // Show message if no results
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        if (visibleRows.length === 0 && searchTerm.length > 0) {
            // You could add a "no results" message here
        }
    });
    
    // Table sorting functionality
    let currentSort = { column: '', direction: 'asc' };
    
    function sortTable(column) {
        const tbody = document.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Toggle sort direction
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }
        
        // Update sort indicators
        updateSortIndicators(column, currentSort.direction);
        
        // Sort rows
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(column) {
                case 'name':
                    aValue = a.dataset.name.toLowerCase();
                    bValue = b.dataset.name.toLowerCase();
                    break;
                case 'rsvp':
                    // Custom order: confirmed, pending, declined, no_response
                    const rsvpOrder = { 'confirmed': 1, 'pending': 2, 'declined': 3, 'no_response': 4 };
                    aValue = rsvpOrder[a.dataset.rsvp] || 4;
                    bValue = rsvpOrder[b.dataset.rsvp] || 4;
                    break;
                case 'age':
                    aValue = parseInt(a.dataset.age) || 0;
                    bValue = parseInt(b.dataset.age) || 0;
                    break;
                case 'language':
                    aValue = a.dataset.language;
                    bValue = b.dataset.language;
                    break;
                default:
                    return 0;
            }
            
            if (currentSort.direction === 'asc') {
                return aValue > bValue ? 1 : (aValue < bValue ? -1 : 0);
            } else {
                return aValue < bValue ? 1 : (aValue > bValue ? -1 : 0);
            }
        });
        
        // Reorder rows in DOM
        rows.forEach(row => tbody.appendChild(row));
    }
    
    function updateSortIndicators(activeColumn, direction) {
        // Reset all indicators
        ['name', 'rsvp', 'age', 'language'].forEach(col => {
            const indicator = document.getElementById(`sort-${col}`);
            if (indicator) {
                if (col === activeColumn) {
                    indicator.textContent = direction === 'asc' ? '↑' : '↓';
                } else {
                    indicator.textContent = '↕️';
                }
            }
        });
    }
</script>
@endpush
@endsection