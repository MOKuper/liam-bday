@extends('layouts.app')

@section('title', 'Manage Gifts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl fun-font text-purple-600 mb-2">Gift Registry</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Back to Dashboard</a>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        🎁
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Gifts</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        ✅
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Claimed</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['claimed'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        🎪
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Available</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['available'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        ⭐
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">High Priority</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['high_priority'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Gift Form -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Add New Gift</h2>
        
        <form action="{{ route('admin.gifts.store') }}" method="POST" class="grid md:grid-cols-2 gap-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gift Name *</label>
                <input type="text" name="name" required 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       value="{{ old('name') }}">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <input type="text" name="category" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       placeholder="e.g., Toys, Books, Clothes"
                       value="{{ old('category') }}">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Describe the gift...">{{ old('description') }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price Range Min (€)</label>
                <input type="number" name="price_range_min" min="0" step="0.01"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       value="{{ old('price_range_min') }}">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price Range Max (€)</label>
                <input type="number" name="price_range_max" min="0" step="0.01"
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       value="{{ old('price_range_max') }}">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Suggestion</label>
                <input type="text" name="store_suggestion" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       placeholder="e.g., Amazon, Bol.com, Local toy store"
                       value="{{ old('store_suggestion') }}">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                <select name="priority" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="1" {{ old('priority') == 1 ? 'selected' : '' }}>High Priority</option>
                    <option value="2" {{ old('priority') == 2 ? 'selected' : '' }}>Medium Priority</option>
                    <option value="3" {{ old('priority') == 3 ? 'selected' : '' }}>Low Priority</option>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                <input type="url" name="image_url" 
                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                       placeholder="https://example.com/image.jpg"
                       value="{{ old('image_url') }}">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Internal notes...">{{ old('notes') }}</textarea>
            </div>
            
            <div class="md:col-span-2 flex items-center">
                <input type="checkbox" name="is_active" value="1" id="is_active" 
                       class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 text-sm text-gray-700">Active (visible to guests)</label>
            </div>
            
            <div class="md:col-span-2">
                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-full">
                    Add Gift to Registry
                </button>
            </div>
        </form>
    </div>

    <!-- Gifts List -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Gift List ({{ $gifts->count() }})</h2>
            <a href="{{ route('gifts.index') }}" target="_blank" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm">
                👀 View Public Registry
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gift</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Claimed By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($gifts as $gift)
                    <tr class="{{ !$gift->is_active ? 'bg-gray-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($gift->image_url)
                                    <img src="{{ $gift->image_url }}" alt="{{ $gift->name }}" 
                                         class="w-10 h-10 rounded-lg object-cover mr-3">
                                @else
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        🎁
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $gift->name }}</div>
                                    @if($gift->store_suggestion)
                                        <div class="text-sm text-gray-500">{{ $gift->store_suggestion }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $gift->category ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $gift->price_range_text ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $gift->priority_color }}">
                                {{ $gift->priority_text }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($gift->is_claimed)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Claimed
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Available
                                </span>
                            @endif
                            
                            @if(!$gift->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 ml-1">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($gift->is_claimed)
                                <div>
                                    <strong>{{ $gift->claimed_by_name }}</strong><br>
                                    <span class="text-xs">{{ $gift->claimed_by_email }}</span><br>
                                    <span class="text-xs text-gray-400">{{ $gift->claimed_at->format('M d, Y') }}</span>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <form action="{{ route('admin.gifts.toggle', $gift) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-blue-600 hover:text-blue-900">
                                    {{ $gift->is_active ? '🚫 Deactivate' : '✅ Activate' }}
                                </button>
                            </form>
                            
                            @if($gift->is_claimed)
                                <form action="{{ route('admin.gifts.unclaim', $gift) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-orange-600 hover:text-orange-900" 
                                            onclick="return confirm('Are you sure you want to unclaim this gift?')">
                                        🔄 Unclaim
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.gifts.destroy', $gift) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                        onclick="return confirm('Are you sure you want to delete this gift?')">
                                    🗑️ Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show success/error messages
    @if(session('success'))
        alert('{{ session('success') }}');
    @endif
    
    @if(session('error'))
        alert('{{ session('error') }}');
    @endif
</script>
@endpush
@endsection