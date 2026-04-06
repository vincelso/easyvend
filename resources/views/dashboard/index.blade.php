@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-500 text-sm mt-0.5">Welcome back, {{ auth()->user()->name }}!</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-200 p-6">
        <p class="text-gray-500 text-sm">Total Sales</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">₱{{ number_format($totalSales, 2) }}</p>
    </div>
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-200 p-6">
        <p class="text-gray-500 text-sm">Total Orders</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalOrders }}</p>
    </div>
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-200 p-6">
        <p class="text-gray-500 text-sm">Today's Sales</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">₱{{ number_format($todaySales, 2) }}</p>
    </div>
    @if($isAdmin)
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-200 p-6">
        <p class="text-gray-500 text-sm">Total Users</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalUsers }}</p>
    </div>
    @endif
</div>

{{-- Recent Transactions --}}
<div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-200 p-6">
    <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left border-b border-gray-100">
                <th class="pb-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Product</th>
                @if($isAdmin)
                <th class="pb-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Cashier</th>
                @endif
                <th class="pb-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Quantity</th>
                <th class="pb-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Total</th>
                <th class="pb-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($recent as $transaction)
            <tr class="hover:bg-gray-50">
                <td class="py-3 font-medium text-gray-900">{{ $transaction->product->name ?? 'N/A' }}</td>
                @if($isAdmin)
                <td class="py-3 text-gray-600">{{ $transaction->user->name ?? 'N/A' }}</td>
                @endif
                <td class="py-3 text-gray-600">{{ $transaction->qty }}</td>
                <td class="py-3 font-semibold text-indigo-700">₱{{ number_format($transaction->total, 2) }}</td>
                <td class="py-3 text-gray-500">{{ $transaction->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-400">No recent transactions.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection