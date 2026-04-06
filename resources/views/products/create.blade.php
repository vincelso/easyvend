@extends('layouts.app')
@section('title', 'Add Product')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Add Product</h1>
    <p class="text-gray-500 text-sm mt-0.5">Add a new product to your catalog.</p>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-lg">
    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        <div class="space-y-5">

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Product Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Century Tuna"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Category</label>
                <select name="category" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                    <option value="">-- Select Category --</option>
                    @foreach(['Beverages','Canned Goods','Condiments','Dairy','Household','Instant Noodles','Personal Care','Snacks','Other'] as $cat)
                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Price (₱)</label>
                    <input type="number" name="price" step="0.01" min="0" value="{{ old('price') }}" required
                           placeholder="0.00"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                    @error('price')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Stock Quantity</label>
                    <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                    @error('stock')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

        </div>

        <div class="flex items-center gap-3 mt-7 pt-5 border-t border-gray-100">
            <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                Save Product
            </button>
            <a href="{{ route('products.index') }}"
               class="px-5 py-2.5 border border-gray-200 text-gray-500 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection