@extends('layouts.app')
@section('title', 'Edit Product')
@section('subtitle', 'Updating: ' . $product->name)

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="space-y-5">

                {{-- Image upload --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        Product Image <span class="text-gray-300 font-normal normal-case">(optional)</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl border border-gray-200 overflow-hidden flex-shrink-0 bg-indigo-100 flex items-center justify-center">
                            @if($product->hasImage())
                            <img id="preview-img" src="{{ $product->imageUrl() }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                            @else
                            <img id="preview-img" src="" alt="" class="w-full h-full object-cover hidden">
                            <span id="preview-initials" class="text-indigo-600 font-bold text-sm uppercase">{{ substr($product->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 space-y-2">
                            <label class="cursor-pointer flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all w-fit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                {{ $product->hasImage() ? 'Change Image' : 'Upload Image' }}
                                <input type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                            </label>
                            @if($product->hasImage())
                            <label class="flex items-center gap-1.5 cursor-pointer text-xs text-red-500 hover:text-red-700">
                                <input type="checkbox" name="remove_image" value="1" class="w-3.5 h-3.5 accent-red-500">
                                Remove current image
                            </label>
                            @endif
                            <p class="text-xs text-gray-400">JPG, PNG, WEBP — max 2MB</p>
                        </div>
                    </div>
                    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Product Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Category</label>
                    <select name="category" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                        @foreach(['Beverages','Canned Goods','Condiments','Dairy','Household','Instant Noodles','Personal Care','Snacks','Other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $product->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Price (&#8369;)</label>
                        <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                        @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Stock Quantity</label>
                        <input type="number" name="stock" min="0" value="{{ old('stock', $product->stock) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                        @error('stock')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        Expiry Date <span class="text-gray-300 font-normal normal-case">(optional)</span>
                    </label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('expiry_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    @if($product->expiry_date)
                    @php $status = $product->expiryStatus(); $days = $product->daysUntilExpiry(); @endphp
                    <div class="mt-2">
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                            {{ $status === 'expired' ? 'bg-red-100 text-red-700' : ($status === 'critical' ? 'bg-red-100 text-red-600' : ($status === 'warning' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700')) }}">
                            {{ $status === 'expired' ? 'Expired on ' . $product->expiry_date->format('M d, Y') : $days . ' day(s) remaining' }}
                        </span>
                    </div>
                    @endif
                </div>

            </div>

            <div class="flex items-center gap-3 mt-7 pt-5 border-t border-gray-100">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                    Update Product
                </button>
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 border border-gray-200 text-gray-500 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const img      = document.getElementById('preview-img');
    const initials = document.getElementById('preview-initials');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.classList.remove('hidden');
            if (initials) initials.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection