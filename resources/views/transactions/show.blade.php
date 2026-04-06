@extends('layouts.app')
@section('title', 'Transaction Details')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Transaction Details</h1>
    <p class="text-gray-500 text-sm mt-0.5">Record <span class="font-semibold text-indigo-600">#{{ $transaction->id }}</span></p>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-lg space-y-4">

    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Product</p>
            <p class="text-gray-900 font-semibold mt-0.5">{{ $transaction->product->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Category</p>
            <p class="text-gray-700 mt-0.5">{{ $transaction->product->category ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Unit Price</p>
            <p class="text-gray-700 mt-0.5">₱{{ number_format($transaction->price, 2) }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Quantity</p>
            <p class="text-gray-700 mt-0.5">{{ $transaction->qty }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Total</p>
            <p class="text-indigo-700 font-bold text-lg mt-0.5">₱{{ number_format($transaction->total, 2) }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Payment</p>
            <p class="mt-0.5">
                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">
                    {{ $transaction->payment_method }}
                </span>
            </p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Cashier</p>
            <p class="text-gray-700 mt-0.5">{{ $transaction->user->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Date</p>
            <p class="text-gray-700 mt-0.5">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
        </div>
    </div>

    <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
        <a href="{{ route('transactions.edit', $transaction) }}"
           class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
            Edit
        </a>
        <a href="{{ route('transactions.index') }}"
           class="px-4 py-2 border border-gray-200 text-gray-500 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all">
            Back to List
        </a>
    </div>
</div>
@endsection