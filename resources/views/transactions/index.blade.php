@extends('layouts.app')
@section('title', 'Transactions')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>
        <p class="text-gray-500 text-sm mt-0.5">
            {{ auth()->user()->isAdmin() ? 'All transactions' : 'Your transactions' }}
        </p>
    </div>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('transactions.index') }}" class="mb-4">
    <div class="flex gap-2">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search product..."
               class="border border-gray-200 rounded-xl px-4 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
        <button type="submit"
                class="px-4 py-2 bg-gray-700 text-white text-sm rounded-xl hover:bg-gray-800 transition-all">
            Search
        </button>
        @if($search)
        <a href="{{ route('transactions.index') }}"
           class="px-4 py-2 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50">
            Clear
        </a>
        @endif
        <a href="{{ route('transactions.export', ['search' => $search]) }}"
        class="flex items-center gap-1.5 px-3 py-2 bg-gray-700 text-white text-xs font-semibold rounded-xl hover:bg-gray-800 transition-all">
            📋 Export PDF
        </a>
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">#</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Product</th>
                @if(auth()->user()->isAdmin())
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Cashier</th>
                @endif
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Qty</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Price</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Total</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Payment</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Date</th>
                @if(auth()->user()->isAdmin())
                <th class="px-5 py-3"></th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transactions as $t)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3 text-gray-400">{{ $t->id }}</td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $t->product->name ?? '—' }}</td>
                @if(auth()->user()->isAdmin())
                <td class="px-5 py-3 text-gray-600">{{ $t->user->name ?? '—' }}</td>
                @endif
                <td class="px-5 py-3 text-gray-600">{{ $t->qty }}</td>
                <td class="px-5 py-3 text-gray-600">₱{{ number_format($t->price, 2) }}</td>
                <td class="px-5 py-3 font-semibold text-indigo-700">₱{{ number_format($t->total, 2) }}</td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                        {{ $t->payment_method }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $t->created_at->format('M d, Y') }}</td>
                @if(auth()->user()->isAdmin())
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('transactions.destroy', $t) }}"
                          onsubmit="return confirm('Delete this transaction record?')"
                          style="display:contents">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:underline font-medium">
                            Delete
                        </button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-5 py-8 text-center text-gray-400">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($transactions->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection