@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Hello, ' . auth()->user()->name . '! Here\'s your overview.')

@section('content')

{{-- Low stock alert banner --}}
@if($lowStockProducts->count() > 0 && auth()->user()->isAdmin())
<div class="mb-5 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div class="flex-1">
        <p class="text-amber-800 font-semibold text-sm">⚠ Low Stock Alert</p>
        <p class="text-amber-700 text-xs mt-0.5">
            {{ $lowStockProducts->count() }} product(s) are running low:
            <span class="font-semibold">{{ $lowStockProducts->pluck('name')->join(', ') }}</span>
        </p>
    </div>
    <a href="{{ route('products.index') }}"
       class="text-amber-700 text-xs font-semibold hover:underline flex-shrink-0">
        Manage →
    </a>
</div>
@endif

{{-- Out of stock banner --}}
@if($outOfStockProducts->count() > 0 && auth()->user()->isAdmin())
<div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-start gap-3">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    <div class="flex-1">
        <p class="text-red-700 font-semibold text-sm">🚫 Out of Stock</p>
        <p class="text-red-600 text-xs mt-0.5">
            {{ $outOfStockProducts->count() }} product(s) are out of stock:
            <span class="font-semibold">{{ $outOfStockProducts->pluck('name')->join(', ') }}</span>
        </p>
    </div>
</div>
@endif

{{-- Cashier RBAC notice --}}
@if(auth()->user()->isCashier())
<div class="mb-5 flex items-center gap-2 bg-indigo-50 border border-indigo-200 text-indigo-700 px-4 py-3 rounded-xl text-sm">
    🔒 You are logged in as <strong class="mx-1">Cashier</strong>. You can only view and manage your own transactions.
</div>
@endif

{{-- Stats grid --}}
<div class="grid grid-cols-2 {{ $isAdmin ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Sales</span>
            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalSales, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">All time</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Orders</span>
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
        <p class="text-xs text-gray-400 mt-1">Transactions</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Today's Sales</span>
            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($todaySales, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ now()->format('M d, Y') }}</p>
    </div>

    @if($isAdmin)
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Users</span>
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
        <p class="text-xs text-gray-400 mt-1">Registered accounts</p>
    </div>
    @endif
</div>

{{-- Recent Transactions + Quick actions --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Recent Transactions --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900 text-sm">Recent Transactions</h2>
            <a href="{{ route('transactions.index') }}"
               class="text-xs text-indigo-600 hover:underline font-medium">View all →</a>
        </div>
        @if($recent->isEmpty())
        <div class="text-center py-12 text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">No transactions yet.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Product</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Qty</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Total</th>
                        @if($isAdmin)
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Cashier</th>
                        @endif
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recent as $t)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 font-medium text-gray-800 text-xs">{{ $t->product->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $t->qty }}</td>
                        <td class="px-5 py-3 font-bold text-indigo-600 text-xs">₱{{ number_format($t->total, 2) }}</td>
                        @if($isAdmin)
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $t->user->name ?? '—' }}</td>
                        @endif
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $t->created_at->format('M d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Quick actions + Stock alerts --}}
    <div class="space-y-4">

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h2 class="font-bold text-gray-900 text-sm mb-3">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('orders.create') }}"
                class="flex items-center gap-2 w-full px-3 py-2.5 bg-indigo-50 text-indigo-700 rounded-xl text-sm font-medium hover:bg-indigo-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Order
                </a>
                <a href="{{ route('transactions.index') }}"
                class="flex items-center gap-2 w-full px-3 py-2.5 bg-gray-50 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    View All Transactions
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('reports.index') }}"
                class="flex items-center gap-2 w-full px-3 py-2.5 bg-gray-50 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View Reports
                </a>
                <a href="{{ route('products.create') }}"
                class="flex items-center gap-2 w-full px-3 py-2.5 bg-gray-50 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New Product
                </a>
                @endif
            </div>
        </div>

        {{-- Low stock panel --}}
        @if($isAdmin && $lowStockProducts->count() > 0)
        <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-5">
            <h2 class="font-bold text-amber-700 text-sm mb-3">⚠ Low Stock Items</h2>
            <div class="space-y-2">
                @foreach($lowStockProducts->take(5) as $p)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-700 font-medium truncate">{{ $p->name }}</span>
                    <span class="text-xs font-bold {{ $p->stock <= 5 ? 'text-red-600' : 'text-amber-600' }} flex-shrink-0 ml-2">
                        {{ $p->stock }} left
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection