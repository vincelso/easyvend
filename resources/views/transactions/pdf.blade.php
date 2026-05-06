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
    .badge-blue { background: #ede9fe; color: #4f46e5; }
    .summary { display: table; width: 100%; margin-bottom: 16px; }
    .summary-box { display: table-cell; width: 33%; padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb; text-align: center; }
    .summary-box .label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
    .summary-box .value { font-size: 18px; font-weight: bold; color: #4f46e5; margin-top: 3px; }
    .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af; }
</style>
</head>
<body>
<div class="header">
    <div style="display:table; width:100%">
        <div style="display:table-cell; vertical-align:middle; width:120px;">
            <img src="{{ public_path('images/logo.png') }}" style="height:50px; width:auto;">
        </div>
        <div style="display:table-cell; vertical-align:middle;">
            <h1>EasyVend — Transactions Report</h1>
            <p>Generated on {{ $generatedAt }} by {{ $generatedBy }}</p>
        </div>
    </div>
</div>
<div class="container">
    <div class="meta">
        @if($search) Search: "<span>{{ $search }}</span>" &nbsp;|&nbsp; @endif
        @if(!empty($category)) Category: <span>{{ $category }}</span> &nbsp;|&nbsp; @endif
        @if(!empty($dateFrom) || !empty($dateTo))
            Period: <span>{{ $dateFrom ?: 'All time' }}</span> to <span>{{ $dateTo ?: 'present' }}</span> &nbsp;|&nbsp;
        @endif
        Total records: <span>{{ $transactions->count() }}</span>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Transactions</div>
            <div class="value">{{ $transactions->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Items Sold</div>
            <div class="value">{{ $transactions->sum('qty') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Revenue</div>
            <div class="value">P{{ number_format($transactions->sum('total'), 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Cashier</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ $t->id }}</td>
                <td style="font-weight:bold">{{ $t->product->name ?? '—' }}</td>
                <td>{{ $t->user->name ?? '—' }}</td>
                <td>{{ $t->qty }}</td>
                <td>P{{ number_format($t->price, 2) }}</td>
                <td style="font-weight:bold; color:#4f46e5">P{{ number_format($t->total, 2) }}</td>
                <td><span class="badge badge-blue">{{ $t->payment_method }}</span></td>
                <td>{{ $t->created_at->format('M d, Y h:i A') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center; padding:20px; color:#9ca3af">No transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">EasyVend POS System &nbsp;|&nbsp; {{ $generatedAt }} &nbsp;|&nbsp; Confidential</div>
</div>
</body>
</html>