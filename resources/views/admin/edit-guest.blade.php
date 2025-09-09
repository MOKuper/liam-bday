@extends('layouts.app')

@section('title', 'Edit Guest')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl fun-font text-purple-600 mb-2">Edit Guest</h1>
        <a href="{{ route('admin.guests') }}" class="text-gray-600 hover:text-gray-800">← Back to Guest List</a>
    </div>

    <!-- Edit Guest Form -->
    <div class="bg-white rounded-xl shadow-md p-6 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Edit {{ $guest->name }}</h2>
        
        <form action="{{ route('admin.guests.update', $guest) }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guest Name *</label>
                <input type="text" name="name" required value="{{ old('name', $guest->name) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Is Child?</label>
                <select name="is_child" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="1" {{ old('is_child', $guest->is_child) ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ !old('is_child', $guest->is_child) ? 'selected' : '' }}>No</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $guest->email) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $guest->phone) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $guest->date_of_birth?->format('Y-m-d')) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">Used for age calculation and age-restricted services</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Name (English)</label>
                <input type="text" name="parent_name" value="{{ old('parent_name', $guest->parent_name) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       placeholder="e.g., Mom, Dad">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Name (Dutch)</label>
                <input type="text" name="parent_name_nl" value="{{ old('parent_name_nl', $guest->parent_name_nl) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       placeholder="e.g., Mama, Papa">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Email (English)</label>
                <input type="email" name="parent_email" value="{{ old('parent_email', $guest->parent_email) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Email (Dutch)</label>
                <input type="email" name="parent_email_nl" value="{{ old('parent_email_nl', $guest->parent_email_nl) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Phone (English)</label>
                <input type="text" name="parent_phone" value="{{ old('parent_phone', $guest->parent_phone) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Phone (Dutch)</label>
                <input type="text" name="parent_phone_nl" value="{{ old('parent_phone_nl', $guest->parent_phone_nl) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Language</label>
                <select name="preferred_language" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="en" {{ old('preferred_language', $guest->preferred_language) === 'en' ? 'selected' : '' }}>English</option>
                    <option value="nl" {{ old('preferred_language', $guest->preferred_language) === 'nl' ? 'selected' : '' }}>Dutch (Nederlands)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expected Attendees</label>
                <input type="number" name="expected_attendees" min="1" max="10" value="{{ old('expected_attendees', $guest->expected_attendees) ?? 1 }}"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Friendship Photo</label>
                @if($guest->friendship_photo_path)
                    <div class="mb-4">
                        <img src="{{ Storage::url($guest->friendship_photo_path) }}" 
                             alt="Current photo of {{ $guest->name }} and Liam"
                             class="w-32 h-32 rounded-lg object-cover">
                        <p class="text-xs text-gray-500 mt-1">Current photo</p>
                    </div>
                @else
                    <p class="text-sm text-gray-500 mb-4">No photo uploaded yet</p>
                @endif
                
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload New Friendship Photo (Liam + Guest)</label>
                <button type="button" id="edit-photo-btn" 
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-full text-sm mb-2">
                    📷 Choose & Crop New Photo
                </button>
                <p class="text-xs text-gray-500 mt-1">Click to select and crop a new photo (optional)</p>
                
                <!-- Hidden file input -->
                <input type="file" id="edit-photo-input" accept="image/*" class="hidden">
                <input type="hidden" name="cropped_photo_data" id="cropped-photo-data">
            </div>
            
            <div class="md:col-span-2 flex space-x-4">
                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-full">
                    Update Guest
                </button>
                <a href="{{ route('admin.guests') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-6 rounded-full">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    
    <!-- Photo Crop Modal -->
    <div id="edit-crop-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Crop Friendship Photo</h3>
                
                <div class="mb-4">
                    <div class="bg-gray-100 rounded-lg p-4 max-h-96 overflow-hidden">
                        <img id="edit-crop-image" class="max-w-full" style="display: block;">
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Drag to move, resize the crop area as needed - any aspect ratio</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" id="apply-crop-btn"
                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full text-sm">
                        Apply Crop
                    </button>
                    <button type="button" id="cancel-crop-btn"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-full text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let editCropper = null;
    
    // Handle photo selection for edit form
    document.getElementById('edit-photo-btn').addEventListener('click', function() {
        document.getElementById('edit-photo-input').click();
    });
    
    document.getElementById('edit-photo-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }
        
        // Show cropping modal
        const reader = new FileReader();
        reader.onload = function(event) {
            const image = document.getElementById('edit-crop-image');
            image.src = event.target.result;
            
            // Show modal
            document.getElementById('edit-crop-modal').classList.remove('hidden');
            
            // Initialize cropper
            if (editCropper) {
                editCropper.destroy();
            }
            
            editCropper = new Cropper(image, {
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
    
    // Apply crop
    document.getElementById('apply-crop-btn').addEventListener('click', function() {
        if (!editCropper) return;
        
        const canvas = editCropper.getCroppedCanvas({
            maxWidth: 800,
            maxHeight: 600,
            imageSmoothingQuality: 'high'
        });
        
        // Convert to base64 and store in hidden field
        const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('cropped-photo-data').value = croppedDataUrl;
        
        // Update button text to show photo is selected
        document.getElementById('edit-photo-btn').innerHTML = '✅ New Photo Selected';
        document.getElementById('edit-photo-btn').classList.remove('bg-blue-500', 'hover:bg-blue-600');
        document.getElementById('edit-photo-btn').classList.add('bg-green-500', 'hover:bg-green-600');
        
        // Close modal
        document.getElementById('edit-crop-modal').classList.add('hidden');
        if (editCropper) {
            editCropper.destroy();
            editCropper = null;
        }
    });
    
    // Cancel crop
    document.getElementById('cancel-crop-btn').addEventListener('click', function() {
        document.getElementById('edit-crop-modal').classList.add('hidden');
        document.getElementById('edit-photo-input').value = '';
        if (editCropper) {
            editCropper.destroy();
            editCropper = null;
        }
    });
    
    // Close modal when clicking outside
    document.getElementById('edit-crop-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('cancel-crop-btn').click();
        }
    });
</script>
@endpush
        </form>
    </div>
</div>
@endsection