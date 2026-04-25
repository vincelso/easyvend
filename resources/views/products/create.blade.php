@extends('layouts.app')
@section('title', 'Add Product')
@section('subtitle', 'Add a new product to your catalog')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-5">

                {{-- Image upload --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        Product Image <span class="text-gray-300 font-normal normal-case">(optional)</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <div id="preview-wrapper" class="w-16 h-16 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <svg id="preview-icon" class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <img id="preview-img" src="" alt="" class="w-full h-full object-cover hidden">
                        </div>
                        <div class="flex-1">
                            <label class="cursor-pointer flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-all w-fit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Upload Image
                                <input type="file" name="image" accept="image/*" class="hidden" id="image-input"
                                       onchange="previewImage(this)">
                            </label>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — max 2MB</p>
                        </div>
                    </div>
                    @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Product Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Century Tuna"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-400">If this product already exists, stock will be added instead of creating a duplicate.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Category</label>
                    <select name="category" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                        <option value="">-- Select Category --</option>
                        @foreach(['Beverages','Canned Goods','Condiments','Dairy','Household','Instant Noodles','Personal Care','Snacks','Other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Price (&#8369;)</label>
                        <input type="number" name="price" step="0.01" min="0" value="{{ old('price') }}" placeholder="0.00" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                        @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Stock Quantity</label>
                        <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                        @error('stock')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                        Expiry Date <span class="text-gray-300 font-normal normal-case">(optional)</span>
                    </label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" min="{{ date('Y-m-d') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('expiry_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex items-center gap-3 mt-7 pt-5 border-t border-gray-100">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                    Save Product
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
    const img  = document.getElementById('preview-img');
    const icon = document.getElementById('preview-icon');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.classList.remove('hidden');
            icon.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection