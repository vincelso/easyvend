@extends('layouts.app')
@section('title', 'Products')
@section('subtitle', $products->total() . ' product(s) in catalog')

@section('content')

@if($expiredCount > 0)
<a href="{{ route('products.index', ['filter' => 'expired']) }}" class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm hover:bg-red-100 transition-all">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    <span><strong>{{ $expiredCount }} product(s) have expired</strong> and should be removed from the shelf immediately. Click to view →</span>
</a>
@endif

@if($expiringSoonCount > 0)
<a href="{{ route('products.index', ['filter' => 'expiring']) }}" class="mb-4 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm hover:bg-amber-100 transition-all">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <span><strong>{{ $expiringSoonCount }} product(s) expiring within 30 days.</strong> Consider discounting or prioritizing their sale. Click to view →</span>
</a>
@endif

<div class="flex items-center justify-between flex-wrap gap-3 mb-3">
    <form method="GET" class="flex gap-2 flex-wrap items-center">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Search name or category..."
               class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 w-56">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">Search</button>
        @if($search || $filter)
        <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50 transition-all">Clear</a>
        @endif
    </form>
    <div class="flex items-center gap-2">
        <a href="{{ route('products.export', ['search' => $search, 'filter' => $filter]) }}"
           class="px-3 py-2 bg-gray-700 text-white text-xs font-semibold rounded-xl hover:bg-gray-800 transition-all">
            📋 Export PDF
        </a>
        <a href="{{ route('products.create') }}" class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
    </div>
</div>

{{-- Filter buttons on their own row --}}
<div class="flex items-center gap-2 flex-wrap mb-5">
    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Filter:</span>
    <a href="{{ route('products.index', ['filter' => 'low', 'search' => $search]) }}"
       class="px-3 py-1.5 text-xs font-semibold rounded-xl border transition-all
           {{ $filter === 'low' ? 'bg-amber-500 text-white border-amber-500' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}">
        ⚠ Low Stock
    </a>
    <a href="{{ route('products.index', ['filter' => 'out', 'search' => $search]) }}"
       class="px-3 py-1.5 text-xs font-semibold rounded-xl border transition-all
           {{ $filter === 'out' ? 'bg-red-500 text-white border-red-500' : 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100' }}">
        🚫 Out of Stock
    </a>
    <a href="{{ route('products.index', ['filter' => 'expiring', 'search' => $search]) }}"
       class="px-3 py-1.5 text-xs font-semibold rounded-xl border transition-all
           {{ $filter === 'expiring' ? 'bg-orange-500 text-white border-orange-500' : 'bg-orange-50 text-orange-600 border-orange-200 hover:bg-orange-100' }}">
        ⏰ Expiring Soon
    </a>
    <a href="{{ route('products.index', ['filter' => 'expired', 'search' => $search]) }}"
       class="px-3 py-1.5 text-xs font-semibold rounded-xl border transition-all
           {{ $filter === 'expired' ? 'bg-red-700 text-white border-red-700' : 'bg-red-50 text-red-700 border-red-300 hover:bg-red-100' }}">
        🔴 Expired
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    @if($products->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <p class="font-medium text-sm">No products found.</p>
        <a href="{{ route('products.create') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:underline">Add your first product →</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Expiry</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($products as $product)
                @php $status = $product->expiryStatus(); @endphp
                <tr class="hover:bg-gray-50 transition-colors {{ $status === 'expired' ? 'bg-red-50/50' : '' }}">
                    <td class="px-4 py-3">
                        @if($product->hasImage())
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}"
                             class="w-12 h-12 object-cover rounded-xl border border-gray-200 shadow-sm">
                        @else
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center border border-indigo-200">
                            <span class="text-indigo-600 font-bold text-sm uppercase">{{ substr($product->name, 0, 2) }}</span>
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $product->name }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">{{ $product->category }}</span>
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-700">&#8369;{{ number_format($product->price, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $product->stock <= 0 ? 'bg-red-100 text-red-600' : ($product->stock <= 10 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                            {{ $product->stock <= 0 ? 'Out of Stock' : $product->stock . ' left' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if(!$product->expiry_date)
                        <span class="text-xs text-gray-300">—</span>
                        @else
                        @php $days = $product->daysUntilExpiry(); @endphp
                        <div>
                            <span class="text-xs font-medium {{ $status === 'expired' ? 'text-red-600' : ($status === 'critical' ? 'text-red-500' : ($status === 'warning' ? 'text-amber-600' : 'text-gray-600')) }}">
                                {{ $product->expiry_date->format('M d, Y') }}
                            </span>
                            <span class="block text-xs mt-0.5">
                                @if($status === 'expired')
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded-full font-semibold">Expired</span>
                                @elseif($status === 'critical')
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-600 rounded-full font-semibold">{{ $days }}d left</span>
                                @elseif($status === 'warning')
                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded-full font-semibold">{{ $days }}d left</span>
                                @else
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded-full font-semibold">{{ $days }}d left</span>
                                @endif
                            </span>
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('products.edit', $product) }}"
                               class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition-all">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                  onsubmit="return confirm('Delete this product?')" class="contents">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 transition-all">Delete</button>
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
            <span class="px-3 py-1.5 border rounded-lg text-gray-300 cursor-not-allowed text-xs">&#8249;</span>
            @else
            <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1.5 border rounded-lg hover:bg-gray-50 text-xs">&#8249;</a>
            @endif
            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="px-3 py-1.5 border rounded-lg text-xs transition-all {{ $page == $products->currentPage() ? 'bg-indigo-600 text-white border-indigo-600' : 'hover:bg-gray-50' }}">{{ $page }}</a>
            @endforeach
            @if($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1.5 border rounded-lg hover:bg-gray-50 text-xs">&#8250;</a>
            @else
            <span class="px-3 py-1.5 border rounded-lg text-gray-300 cursor-not-allowed text-xs">&#8250;</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>
@endsection