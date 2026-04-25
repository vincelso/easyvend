<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; background: #fff; }

    .header { background: #4f46e5; color: white; padding: 24px 30px; margin-bottom: 24px; }
    .header h1 { font-size: 22px; font-weight: bold; letter-spacing: -0.5px; }
    .header p { font-size: 10px; margin-top: 4px; opacity: 0.85; }
    .header .meta { margin-top: 10px; font-size: 10px; opacity: 0.75; }

    .container { padding: 0 30px 30px; }

    .section { margin-bottom: 24px; }
    .section-title { font-size: 13px; font-weight: bold; color: #4f46e5; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }

    .stats-grid { display: table; width: 100%; margin-bottom: 20px; }
    .stat-box { display: table-cell; width: 33%; padding: 14px; background: #f9fafb; border: 1px solid #e5e7eb; text-align: center; }
    .stat-box .label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-box .value { font-size: 20px; font-weight: bold; color: #4f46e5; margin-top: 4px; }
    .stat-box .sub { font-size: 9px; color: #9ca3af; margin-top: 2px; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    thead tr { background: #4f46e5; color: white; }
    thead th { padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    tbody tr:nth-child(even) { background: #f9fafb; }
    tbody tr { border-bottom: 1px solid #f3f4f6; }
    tbody td { padding: 8px 10px; font-size: 10px; }

    .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: bold; }
    .badge-red { background: #fee2e2; color: #dc2626; }
    .badge-amber { background: #fef3c7; color: #d97706; }
    .badge-green { background: #dcfce7; color: #16a34a; }
    .badge-blue { background: #ede9fe; color: #4f46e5; }

    .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }

    .page-break { page-break-after: always; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .font-bold { font-weight: bold; }
    .text-indigo { color: #4f46e5; }
    .text-red { color: #dc2626; }
    .text-amber { color: #d97706; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>EasyVend — Report</h1>
    <p>Generated on {{ $generatedAt }} by {{ $generatedBy }}</p>
    <div class="meta">Period: Last {{ $period }} days &nbsp;|&nbsp; Report type: {{ ucfirst($type) }}</div>
</div>

<div class="container">

{{-- ── SALES REPORT ── --}}
@if($type === 'sales' || $type === 'all')

<div class="section">
    <div class="section-title">Sales Summary</div>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Total Revenue</div>
            <div class="value">P{{ number_format($totalRevenue, 2) }}</div>
            <div class="sub">Last {{ $period }} days</div>
        </div>
        <div class="stat-box">
            <div class="label">Total Transactions</div>
            <div class="value">{{ $totalOrders }}</div>
            <div class="sub">Processed</div>
        </div>
        <div class="stat-box">
            <div class="label">Avg. Order Value</div>
            <div class="value">P{{ number_format($avgOrder, 2) }}</div>
            <div class="sub">Per transaction</div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-title">Top 5 Products by Units Sold</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th class="text-right">Units Sold</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="font-bold">{{ $p->name }}</td>
                <td class="text-right">{{ $p->total_qty }}</td>
                <td class="text-right text-indigo font-bold">P{{ number_format($p->total_revenue, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">No data available.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="section">
    <div class="section-title">Payment Methods Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th class="text-right">Transactions</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentBreakdown as $p)
            <tr>
                <td><span class="badge badge-blue">{{ $p->payment_method }}</span></td>
                <td class="text-right">{{ $p->count }}</td>
                <td class="text-right font-bold">P{{ number_format($p->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center">No data available.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($cashierStats->count() > 0)
<div class="section">
    <div class="section-title">Sales by Cashier</div>
    <table>
        <thead>
            <tr>
                <th>Cashier</th>
                <th class="text-right">Transactions</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashierStats as $c)
            <tr>
                <td class="font-bold">{{ $c->name }}</td>
                <td class="text-right">{{ $c->total_orders }}</td>
                <td class="text-right text-indigo font-bold">P{{ number_format($c->total_revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($type === 'all') <div class="page-break"></div> @endif
@endif

{{-- ── STOCK REPORT ── --}}
@if($type === 'stock' || $type === 'all')

<div class="section">
    <div class="section-title">Inventory Summary</div>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Total Products</div>
            <div class="value">{{ $stockSummary->total }}</div>
            <div class="sub">In catalog</div>
        </div>
        <div class="stat-box">
            <div class="label">Low Stock</div>
            <div class="value" style="color:#d97706">{{ $stockSummary->low_stock }}</div>
            <div class="sub">1–10 units left</div>
        </div>
        <div class="stat-box">
            <div class="label">Out of Stock</div>
            <div class="value" style="color:#dc2626">{{ $stockSummary->out_of_stock }}</div>
            <div class="sub">Need restocking</div>
        </div>
    </div>
</div>

@if($lowStockItems->count() > 0)
<div class="section">
    <div class="section-title">Low Stock Items (1–10 units)</div>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lowStockItems as $p)
            <tr>
                <td class="font-bold">{{ $p->name }}</td>
                <td>{{ $p->category }}</td>
                <td class="text-right"><span class="badge badge-amber">{{ $p->stock }} left</span></td>
                <td class="text-right">P{{ number_format($p->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($outOfStockItems->count() > 0)
<div class="section">
    <div class="section-title">Out of Stock Items</div>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th class="text-right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outOfStockItems as $p)
            <tr>
                <td class="font-bold">{{ $p->name }}</td>
                <td>{{ $p->category }}</td>
                <td class="text-right">P{{ number_format($p->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($type === 'all') <div class="page-break"></div> @endif
@endif

{{-- ── USERS REPORT (admin only) ── --}}
@if(($type === 'users' || $type === 'all') && $userStats->count() > 0)
<div class="section">
    <div class="section-title">User Activity</div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th class="text-right">Transactions</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userStats as $u)
            <tr>
                <td class="font-bold">{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="badge {{ $u->role === 'admin' ? 'badge-blue' : 'badge-green' }}">{{ ucfirst($u->role) }}</span></td>
                <td class="text-right">{{ $u->transactions_count }}</td>
                <td>{{ $u->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="footer">
    EasyVend POS System &nbsp;|&nbsp; Report generated {{ $generatedAt }} &nbsp;|&nbsp; Confidential
</div>

</div>
</body>
</html>