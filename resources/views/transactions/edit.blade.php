@extends('layouts.app')
@section('title', 'Edit Transaction')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Edit Transaction</h1>
    <p class="text-gray-500 text-sm mt-0.5">Updating record <span class="font-semibold text-indigo-600">#{{ $transaction->id }}</span></p>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-lg">
    <form method="POST" action="{{ route('transactions.update', $transaction) }}">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            {{-- Product --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                    Product
                </label>
                <select name="product_id" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                    @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        {{ old('product_id', $transaction->product_id) == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} — ₱{{ number_format($product->price, 2) }} ({{ $product->stock }} in stock)
                    </option>
                    @endforeach
                </select>
                @error('product_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quantity --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                    Quantity
                </label>
                <input type="number" name="qty" min="1"
                       value="{{ old('qty', $transaction->qty) }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                @error('qty')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">
                    Payment Method
                </label>
                <select name="payment_method" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                    @foreach(['Cash', 'GCash', 'Maya', 'Card'] as $method)
                    <option value="{{ $method }}"
                        {{ old('payment_method', $transaction->payment_method) == $method ? 'selected' : '' }}>
                        {{ $method }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Current info box --}}
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 text-sm text-indigo-700">
                <p class="font-semibold mb-1">Current Record</p>
                <p>Product: <span class="font-medium">{{ $transaction->product->name ?? '—' }}</span></p>
                <p>Qty: <span class="font-medium">{{ $transaction->qty }}</span> |
                   Total: <span class="font-medium">₱{{ number_format($transaction->total, 2) }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-3 mt-7 pt-5 border-t border-gray-100">
            <button type="submit"
                    class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                Update Transaction
            </button>
            <a href="{{ route('transactions.index') }}"
               class="px-5 py-2.5 border border-gray-200 text-gray-500 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection