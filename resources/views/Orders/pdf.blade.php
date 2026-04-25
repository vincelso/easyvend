<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
    .header { background: #4f46e5; color: white; padding: 20px 30px; margin-bottom: 20px; }
    .header h1 { font-size: 20px; font-weight: bold; }
    .header p { font-size: 10px; margin-top: 4px; opacity: 0.85; }
    .container { padding: 0 30px 30px; }
    .meta { margin-bottom: 16px; font-size: 10px; color: #6b7280; }
    .meta span { font-weight: bold; color: #1f2937; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #4f46e5; color: white; }
    thead th { padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    tbody tr:nth-child(even) { background: #f9fafb; }
    tbody tr { border-bottom: 1px solid #f3f4f6; }
    tbody td { padding: 8px 10px; font-size: 10px; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 999px; font-size: 9px; font-weight: bold; }
    .badge-green { background: #dcfce7; color: #16a34a; }
    .badge-amber { background: #fef3c7; color: #d97706; }
    .summary { display: table; width: 100%; margin-bottom: 16px; }
    .summary-box { display: table-cell; width: 33%; padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb; text-align: center; }
    .summary-box .label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
    .summary-box .value { font-size: 18px; font-weight: bold; color: #4f46e5; margin-top: 3px; }
    .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>
<div class="header">
    <h1>EasyVend — Orders Report</h1>
    <p>Generated on {{ $generatedAt }} by {{ $generatedBy }}</p>
</div>
<div class="container">
    <div class="meta">
        @if($search) Filter: Customer name contains "<span>{{ $search }}</span>" &nbsp;|&nbsp; @endif
        @if($status) Status: <span>{{ ucfirst($status) }}</span> &nbsp;|&nbsp; @endif
        Total records: <span>{{ $orders->count() }}</span>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Orders</div>
            <div class="value">{{ $orders->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Completed</div>
            <div class="value" style="color:#16a34a">{{ $orders->where('status','completed')->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Grand Total Revenue</div>
            <div class="value">P{{ number_format($orders->where('status','completed')->sum('grand_total'), 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Cashier</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td style="font-weight:bold">{{ $order->customer_name }}</td>
                <td>{{ $order->items->count() }} item(s)</td>
                <td style="font-weight:bold; color:#4f46e5">P{{ number_format($order->grand_total, 2) }}</td>
                <td>{{ $order->payment_method }}</td>
                <td>{{ $order->user->name ?? '—' }}</td>
                <td><span class="badge {{ $order->status === 'completed' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($order->status) }}</span></td>
                <td>{{ $order->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center; padding:20px; color:#9ca3af">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">EasyVend POS System &nbsp;|&nbsp; {{ $generatedAt }} &nbsp;|&nbsp; Confidential</div>
</div>
</body>
</html>