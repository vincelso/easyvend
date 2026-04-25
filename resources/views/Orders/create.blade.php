@extends('layouts.app')
@section('title', 'New Sale / POS')
@section('subtitle', 'Add items to a new or existing order')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- LEFT: New Order Form --}}
    <div>
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">New Order</h2>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <form method="POST" action="{{ route('orders.store') }}" id="order-form">
                @csrf

                {{-- Customer Name --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', 'Walk-in Customer') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                    @error('customer_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Payment Method --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Payment Method</label>
                    <select name="payment_method" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:bg-white transition-all">
                        @foreach(['Cash', 'GCash', 'Maya', 'Card'] as $m)
                        <option value="{{ $m }}" {{ old('payment_method','Cash') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Cart Items --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Items</label>
                    <div id="cart-items" class="space-y-2">
                        <div class="cart-row flex items-center gap-2">
                            <select name="items[0][product_id]" required
                                    class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 product-select">
                                <option value="">-- Select product --</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-stock="{{ $p->stock }}">
                                    {{ $p->name }} — &#8369;{{ number_format($p->price, 2) }} ({{ $p->stock }} left)
                                </option>
                                @endforeach
                            </select>
                            <input type="number" name="items[0][qty]" min="1" value="1" required
                                   class="w-20 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 qty-input">
                            <span class="text-xs text-gray-400 w-20 text-right subtotal-display">&#8369;0.00</span>
                            <button type="button" onclick="removeRow(this)"
                                    class="text-red-400 hover:text-red-600 transition-colors text-lg leading-none flex-shrink-0">
                                &times;
                            </button>
                        </div>
                    </div>

                    <button type="button" onclick="addRow()"
                            class="mt-3 flex items-center gap-1.5 text-indigo-600 text-sm font-medium hover:text-indigo-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Another Product
                    </button>
                </div>

                @error('items')
                <div class="mb-3 text-xs text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ $message }}</div>
                @enderror

                {{-- Grand Total display --}}
                <div class="flex items-center justify-between py-3 border-t border-gray-100 mb-4">
                    <span class="font-bold text-gray-700">Grand Total</span>
                    <span id="grand-total" class="text-xl font-black text-indigo-600">&#8369;0.00</span>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all text-sm shadow-sm">
                    &#128722; Save Order
                </button>
            </form>
        </div>
    </div>

    {{-- RIGHT: Open Orders --}}
    <div>
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">
            Open Orders
            <span class="text-gray-400 font-normal normal-case">(click to add more items)</span>
        </h2>

        @if($openOrders->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center text-gray-400">
            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">No open orders yet.</p>
        </div>
        @else
        <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
            @foreach($openOrders as $order)
            <a href="{{ route('orders.show', $order) }}"
               class="block bg-white rounded-2xl border border-amber-200 shadow-sm p-4 hover:border-indigo-300 hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <span class="font-bold text-gray-800 text-sm">{{ $order->customer_name }}</span>
                        <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-semibold">Open</span>
                    </div>
                    <span class="font-black text-indigo-600 text-sm">&#8369;{{ number_format($order->grand_total, 2) }}</span>
                </div>
                <div class="space-y-0.5">
                    @foreach($order->items->take(3) as $item)
                    <p class="text-xs text-gray-500">
                        {{ $item->product->name ?? '—' }} &times; {{ $item->qty }}
                        <span class="text-gray-400">= &#8369;{{ number_format($item->subtotal, 2) }}</span>
                    </p>
                    @endforeach
                    @if($order->items->count() > 3)
                    <p class="text-xs text-indigo-400">+{{ $order->items->count() - 3 }} more items...</p>
                    @endif
                </div>
                <p class="text-xs text-gray-400 mt-2">{{ $order->payment_method }} · {{ $order->created_at->format('M d, h:i A') }}</p>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
let rowCount = 1;
const productOptions = `{!! $products->map(fn($p) => '<option value="'.$p->id.'" data-price="'.$p->price.'" data-stock="'.$p->stock.'">'.e($p->name).' — ₱'.number_format($p->price,2).' ('.$p->stock.' left)</option>')->join('') !!}`;

function addRow() {
    const container = document.getElementById('cart-items');
    const div = document.createElement('div');
    div.className = 'cart-row flex items-center gap-2';
    div.innerHTML = `
        <select name="items[${rowCount}][product_id]" required
                class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 product-select">
            <option value="">-- Select product --</option>
            ${productOptions}
        </select>
        <input type="number" name="items[${rowCount}][qty]" min="1" value="1" required
               class="w-20 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 qty-input">
        <span class="text-xs text-gray-400 w-20 text-right subtotal-display">&#8369;0.00</span>
        <button type="button" onclick="removeRow(this)"
                class="text-red-400 hover:text-red-600 transition-colors text-lg leading-none flex-shrink-0">&times;</button>
    `;
    container.appendChild(div);
    bindRow(div);
    rowCount++;
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.cart-row');
    if (rows.length <= 1) return;
    btn.closest('.cart-row').remove();
    updateGrandTotal();
}

function bindRow(row) {
    const sel = row.querySelector('.product-select');
    const qty = row.querySelector('.qty-input');
    const sub = row.querySelector('.subtotal-display');

    function calc() {
        const opt   = sel.options[sel.selectedIndex];
        const price = parseFloat(opt?.dataset?.price || 0);
        const q     = parseInt(qty.value) || 0;
        const total = price * q;
        sub.textContent = '₱' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        updateGrandTotal();
    }
    sel.addEventListener('change', calc);
    qty.addEventListener('input', calc);
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-display').forEach(el => {
        total += parseFloat(el.textContent.replace(/[₱,]/g, '')) || 0;
    });
    document.getElementById('grand-total').textContent =
        '₱' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Bind existing rows on load
document.querySelectorAll('.cart-row').forEach(bindRow);
</script>
@endsection