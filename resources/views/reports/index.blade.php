@extends('layouts.app')
@section('title', 'Reports & Analytics')
@section('subtitle', 'Sales performance overview')

@section('content')

{{-- Period filter --}}
<div class="flex items-center gap-3 mb-3">
    <span class="text-sm text-gray-500 font-medium">Show data for:</span>
    @foreach([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 90 days'] as $days => $label)
    <a href="{{ route('reports.index', ['period' => $days]) }}"
       class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all
              {{ $period == $days ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- Export buttons --}}
<div class="flex items-center gap-2 mb-6 flex-wrap">
    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Export PDF:</span>
    <a href="{{ route('reports.export', ['period' => $period, 'type' => 'sales']) }}"
       class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition-all">
        📊 Sales Report
    </a>
    <a href="{{ route('reports.export', ['period' => $period, 'type' => 'stock']) }}"
       class="px-3 py-1.5 bg-amber-500 text-white text-xs font-semibold rounded-lg hover:bg-amber-600 transition-all">
        📦 Stock Report
    </a>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('reports.export', ['period' => $period, 'type' => 'users']) }}"
       class="px-3 py-1.5 bg-purple-600 text-white text-xs font-semibold rounded-lg hover:bg-purple-700 transition-all">
        👥 User Report
    </a>
    <a href="{{ route('reports.export', ['period' => $period, 'type' => 'all']) }}"
       class="px-3 py-1.5 bg-gray-700 text-white text-xs font-semibold rounded-lg hover:bg-gray-800 transition-all">
        📋 Full Report
    </a>
    @endif
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Revenue</p>
        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($totalRevenue, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Last {{ $period }} days</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Total Orders</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
        <p class="text-xs text-gray-400 mt-1">Transactions processed</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Avg. Order Value</p>
        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($avgOrder, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Per transaction</p>
    </div>
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Daily Sales Chart --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <h2 class="font-bold text-gray-900 text-sm mb-4">Daily Sales (Last {{ $period }} Days)</h2>
        @if($dailySales->isEmpty())
        <div class="flex items-center justify-center h-48 text-gray-400 text-sm">No data available.</div>
        @else
        <div class="relative h-52">
            <canvas id="dailySalesChart"></canvas>
        </div>
        @endif
    </div>

    {{-- Top Products Chart --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <h2 class="font-bold text-gray-900 text-sm mb-4">Top 5 Products by Units Sold</h2>
        @if($topProducts->isEmpty())
        <div class="flex items-center justify-center h-48 text-gray-400 text-sm">No data available.</div>
        @else
        <div class="relative h-52">
            <canvas id="topProductsChart"></canvas>
        </div>
        @endif
    </div>
</div>

{{-- Tables row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Top Products Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900 text-sm">Top Selling Products</h2>
        </div>
        @if($topProducts->isEmpty())
        <div class="text-center py-10 text-gray-400 text-sm">No sales recorded.</div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Product</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Units</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($topProducts as $i => $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center
                            {{ $i === 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $i + 1 }}
                        </span>
                    </td>
                    <td class="px-5 py-3 font-medium text-gray-800 text-xs">{{ $p->name }}</td>
                    <td class="px-5 py-3 text-gray-600 text-xs font-semibold">{{ $p->total_qty }}</td>
                    <td class="px-5 py-3 text-indigo-600 font-bold text-xs">₱{{ number_format($p->total_revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Payment breakdown + Cashier stats --}}
    <div class="space-y-5">
        {{-- Payment method breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900 text-sm">Payment Methods</h2>
            </div>
            @if($paymentBreakdown->isEmpty())
            <div class="text-center py-8 text-gray-400 text-sm">No data.</div>
            @else
            <div class="p-5 space-y-3">
                @foreach($paymentBreakdown as $pm)
                @php $pct = $totalOrders > 0 ? ($pm->count / $totalOrders) * 100 : 0; @endphp
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-medium text-gray-700">{{ $pm->payment_method }}</span>
                        <span class="text-gray-500">{{ $pm->count }} orders · ₱{{ number_format($pm->total, 2) }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-2 bg-indigo-500 rounded-full transition-all"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Cashier performance --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900 text-sm">Cashier Performance</h2>
            </div>
            @if($cashierStats->isEmpty())
            <div class="text-center py-8 text-gray-400 text-sm">No data.</div>
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Cashier</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Orders</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($cashierStats as $cs)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800 text-xs">{{ $cs->name }}</td>
                        <td class="px-5 py-3 text-gray-600 text-xs">{{ $cs->total_orders }}</td>
                        <td class="px-5 py-3 text-indigo-600 font-bold text-xs">₱{{ number_format($cs->total_revenue, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    @if($dailySales->isNotEmpty())
    new Chart(document.getElementById('dailySalesChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySales->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->values()) !!},
            datasets: [{
                label: 'Sales',
                data: {!! json_encode($dailySales->pluck('total')->values()) !!},
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79,70,229,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#4f46e5',
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, callback: v => '\u20B1' + v } }
            }
        }
    });
    @endif

    @if($topProducts->isNotEmpty())
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topProducts->pluck('name')->values()) !!},
            datasets: [{
                label: 'Units Sold',
                data: {!! json_encode($topProducts->pluck('total_qty')->values()) !!},
                backgroundColor: ['#4f46e5','#6366f1','#818cf8','#a5b4fc','#c7d2fe'],
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } }
            }
        }
    });
    @endif

});
</script>
@endpush