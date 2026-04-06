@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>
        <p class="text-gray-500 text-sm mt-0.5">{{ $products->total() }} product(s) in catalog.</p>
    </div>
    <a href="{{ route('products.create') }}"
       class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
        ➕ Add Product
    </a>
</div>

{{-- Search --}}
<form method="GET" class="mb-5 flex gap-2 max-w-sm">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Search name or category..."
           class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
    <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
        Search
    </button>
    @if($search)
    <a href="{{ route('products.index') }}"
       class="px-4 py-2 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50 transition-all">
        Clear
    </a>
    @endif
</form>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    @if($products->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <p class="text-4xl mb-3">📦</p>
        <p class="font-medium">No products found.</p>
        <a href="{{ route('products.create') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">
            Add your first product →
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Stock</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($products as $product)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 font-mono text-xs text-gray-400">#{{ $product->id }}</td>
                    <td class="px-5 py-3 font-semibold text-gray-800">{{ $product->name }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
                            {{ $product->category }}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-semibold text-gray-700">₱{{ number_format($product->price, 2) }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $product->stock <= 0 ? 'bg-red-100 text-red-600' :
                               ($product->stock <= 10 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                            {{ $product->stock <= 0 ? 'Out of Stock' : $product->stock . ' left' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('products.edit', $product) }}"
                               class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition-all">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                  onsubmit="return confirm('Delete {{ $product->name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 transition-all">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
        <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
        <div class="flex gap-1">
            @if($products->onFirstPage())
            <span class="px-3 py-1.5 border rounded-lg text-gray-300 cursor-not-allowed">‹</span>
            @else
            <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1.5 border rounded-lg hover:bg-gray-50">‹</a>
            @endif
            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
            <a href="{{ $url }}"
               class="px-3 py-1.5 border rounded-lg transition-all {{ $page == $products->currentPage() ? 'bg-indigo-600 text-white border-indigo-600' : 'hover:bg-gray-50' }}">
                {{ $page }}
            </a>
            @endforeach
            @if($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1.5 border rounded-lg hover:bg-gray-50">›</a>
            @else
            <span class="px-3 py-1.5 border rounded-lg text-gray-300 cursor-not-allowed">›</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>
@endsection