@extends('layouts.app')
@section('title', 'Sales')
@section('subtitle', 'All customer sales')

@section('content')

<div class="flex items-center justify-between flex-wrap gap-3 mb-5">
    <form method="GET" class="flex gap-2 flex-wrap items-center">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search customer name..."
               class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 w-44">
        <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
            <option value="">All Status</option>
            <option value="open"      {{ $status === 'open'      ? 'selected' : '' }}>Open</option>
            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
        </select>
        <input type="date" name="date_from" value="{{ $dateFrom }}"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
        <span class="text-xs text-gray-400">to</span>
        <input type="date" name="date_to" value="{{ $dateTo }}"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
        <button class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
            Search
        </button>
        @if($search || $status || $dateFrom || $dateTo)
        <a href="{{ route('orders.index') }}" class="px-4 py-2 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50">Clear</a>
        @endif
        <a href="{{ route('orders.export', ['search' => $search, 'status' => $status, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
           class="flex items-center gap-1.5 px-3 py-2 bg-gray-700 text-white text-xs font-semibold rounded-xl hover:bg-gray-800 transition-all">
            📋 Export PDF
        </a>
    </form>
    <a href="{{ route('orders.create') }}"
       class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Sale
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    @if($orders->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <p class="text-3xl mb-2">&#128722;</p>
        <p class="text-sm font-medium">No sales found.</p>
        <a href="{{ route('orders.create') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:underline">Create first sale →</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Customer</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Items</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Total</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Payment</th>
                    @if(auth()->user()->isAdmin())
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Cashier</th>
                    @endif
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 font-mono text-xs text-gray-400">#{{ $order->id }}</td>
                    <td class="px-5 py-3 font-semibold text-gray-800">{{ $order->customer_name }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $order->items->count() }} item(s)</td>
                    <td class="px-5 py-3 font-bold text-indigo-700">&#8369;{{ number_format($order->grand_total, 2) }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">{{ $order->payment_method }}</span>
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $order->user->name ?? '—' }}</td>
                    @endif
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ $order->status === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('orders.show', $order) }}"
                               class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition-all">
                                {{ $order->isOpen() ? 'Add Items' : 'View' }}
                            </a>
                            @if($order->isOpen())
                            <form method="POST" action="{{ route('orders.destroy', $order) }}" class="contents"
                                onsubmit="return confirm('Cancel sale #{{ $order->id }}? Stock will be restored.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 transition-all">
                                    Cancel
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
        <span>Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>
        <div class="flex gap-1">
            @if($orders->onFirstPage())
            <span class="px-3 py-1.5 border rounded-lg text-gray-300 cursor-not-allowed text-xs">&#8249;</span>
            @else
            <a href="{{ $orders->previousPageUrl() }}" class="px-3 py-1.5 border rounded-lg hover:bg-gray-50 text-xs">&#8249;</a>
            @endif
            @if($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}" class="px-3 py-1.5 border rounded-lg hover:bg-gray-50 text-xs">&#8250;</a>
            @else
            <span class="px-3 py-1.5 border rounded-lg text-gray-300 cursor-not-allowed text-xs">&#8250;</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>
@endsection