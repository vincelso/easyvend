@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('subtitle', $order->customer_name . ' · ' . ucfirst($order->status))

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- LEFT: Order details & receipt --}}
    <div class="lg:col-span-3">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">Order #{{ $order->id }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $order->created_at->format('M d, Y h:i A') }} &middot;
                        Cashier: {{ $order->user->name ?? '—' }}
                    </p>

                    {{-- Payment method switcher --}}
                    @if($order->isOpen())
                    <form method="POST" action="{{ route('orders.updatePayment', $order) }}" class="flex items-center gap-2 mt-2">
                        @csrf @method('PATCH')
                        <select name="payment_method"
                                class="border border-gray-200 rounded-lg px-2 py-1 text-xs bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @foreach(['Cash', 'GCash', 'Maya', 'Card', 'Other'] as $method)
                            <option value="{{ $method }}" {{ $order->payment_method === $method ? 'selected' : '' }}>
                                {{ $method }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg hover:bg-indigo-200 transition-all">
                            Update
                        </button>
                    </form>
                    @else
                    <p class="text-xs text-gray-400 mt-1">{{ $order->payment_method }}</p>
                    @endif
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full
                    {{ $order->isOpen() ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            {{-- Items table --}}
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Product</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Price</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Qty</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase">Subtotal</th>
                        @if($order->isOpen())
                        <th class="px-5 py-2.5"></th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($order->items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                @if($item->product && $item->product->hasImage())
                                <img src="{{ $item->product->imageUrl() }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                @else
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-indigo-600 font-bold text-xs">{{ substr($item->product->name ?? '?', 0, 2) }}</span>
                                </div>
                                @endif
                                <span class="font-medium text-gray-800 text-xs">{{ $item->product->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">&#8369;{{ number_format($item->price, 2) }}</td>
                        <td class="px-5 py-3">
                            @if($order->isOpen())
                            {{-- +/- Stepper --}}
                            <div class="flex items-center gap-1">
                                <form method="POST" action="{{ route('orders.updateItemQty', [$order, $item]) }}" style="display:contents">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="qty" value="{{ max(0, $item->qty - 1) }}">
                                    <button type="submit"
                                            class="w-6 h-6 rounded-md bg-gray-100 text-gray-600 hover:bg-red-100 hover:text-red-600 text-sm font-bold transition-all flex items-center justify-center"
                                            {{ $item->qty <= 1 ? 'onclick=\'return confirm("Remove this item?")\'' : '' }}>
                                        −
                                    </button>
                                </form>
                                <span class="text-xs font-bold text-gray-700 w-6 text-center">{{ $item->qty }}</span>
                                <form method="POST" action="{{ route('orders.updateItemQty', [$order, $item]) }}" style="display:contents">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="qty" value="{{ $item->qty + 1 }}">
                                    <button type="submit"
                                            class="w-6 h-6 rounded-md bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-600 text-sm font-bold transition-all flex items-center justify-center">
                                        +
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-gray-600 font-semibold text-xs">&times; {{ $item->qty }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-bold text-indigo-700 text-xs">&#8369;{{ number_format($item->subtotal, 2) }}</td>
                        @if($order->isOpen())
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('orders.removeItem', [$order, $item]) }}" style="display:contents"
                                  onsubmit="return confirm('Remove this item and restore its stock?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs font-medium transition-colors">
                                    Remove
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm">No items in this order.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Grand total --}}
            <div class="px-5 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="font-bold text-gray-700">Grand Total</span>
                <span class="text-xl font-black text-indigo-600">&#8369;{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 mt-4 flex-wrap">
            @if($order->isOpen())
                <form method="POST" action="{{ route('orders.complete', $order) }}" style="display:contents">
                    @csrf
                    <button type="submit"
                            class="px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all shadow-sm"
                            onclick="return confirm('Mark this order as completed?')">
                        ✓ Complete Order
                    </button>
                </form>
            @endif

            {{-- ADD THIS BUTTON --}}
            @if(!$order->isOpen())
            <button onclick="document.getElementById('receipt-modal').classList.remove('hidden')"
                    class="px-5 py-2.5 bg-gray-700 text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition-all shadow-sm">
                🧾 Print Receipt
            </button>
             @endif

            <a href="{{ route('orders.create') }}"
               class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                + New Order
            </a>
            <a href="{{ route('orders.index') }}"
               class="px-5 py-2.5 border border-gray-200 text-gray-500 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all">
                View All Orders
            </a>
            @if(auth()->user()->isAdmin() || $order->user_id === auth()->id())
                <form method="POST" action="{{ route('orders.destroy', $order) }}" style="display:contents"
                      onsubmit="return confirm('Delete this entire order? Stock will be restored.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2.5 border border-red-200 text-red-500 text-sm font-medium rounded-xl hover:bg-red-50 transition-all">
                        Delete Order
                    </button>
                </form>
            @endif
        </div>
    </div>

    

    {{-- RIGHT: Add Item panel (only if open) --}}
    @if($order->isOpen())
    <div class="lg:col-span-2">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">
            Add Item to Order #{{ $order->id }}
        </h2>
        <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-5">
            <form method="POST" action="{{ route('orders.addItem', $order) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Product</label>
                        <select name="product_id" required
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-300 focus:bg-white transition-all">
                            <option value="">-- Select product --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->price }}">
                                {{ $p->name }} — &#8369;{{ number_format($p->price, 2) }} ({{ $p->stock }} left)
                            </option>
                            @endforeach
                        </select>
                        @error('product_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Quantity</label>
                        <input type="number" name="qty" min="1" value="1" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-300 focus:bg-white transition-all">
                        @error('qty')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit"
                        class="w-full mt-5 py-2.5 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-all text-sm">
                    + Add to This Order
                </button>
            </form>
            <p class="text-xs text-gray-400 text-center mt-3">
                Adding to order for <strong>{{ $order->customer_name }}</strong>
            </p>
        </div>
    </div>
    @else
    <div class="lg:col-span-2">
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5 text-center">
            <div class="text-3xl mb-2">&#10003;</div>
            <p class="font-bold text-green-700">Order Completed</p>
            <p class="text-xs text-green-600 mt-1">This order has been finalized and cannot be modified.</p>
        </div>
    </div>
    @endif

</div>

{{-- Receipt Modal --}}
@if(!$order->isOpen())
<div id="receipt-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900 text-sm">Receipt — Order #{{ $order->id }}</h3>
            <button onclick="document.getElementById('receipt-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
        </div>

        {{-- Receipt content --}}
        <div id="receipt-content" class="px-6 py-5">

            {{-- Header --}}
            <div class="text-center mb-4">
                <p class="text-xl font-black tracking-tight text-gray-900">EasyVend</p>
                <p class="text-xs text-gray-400 mt-0.5">Official Receipt</p>
                <div class="border-t border-dashed border-gray-300 mt-3"></div>
            </div>

            {{-- Order info --}}
            <div class="space-y-1 mb-4 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span class="text-gray-400">Order #</span>
                    <span class="font-semibold">{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Date</span>
                    <span class="font-semibold">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Customer</span>
                    <span class="font-semibold">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Cashier</span>
                    <span class="font-semibold">{{ $order->user->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Payment</span>
                    <span class="font-semibold">{{ $order->payment_method }}</span>
                </div>
            </div>

            <div class="border-t border-dashed border-gray-300 mb-4"></div>

            {{-- Items --}}
            <div class="space-y-2 mb-4">
                @foreach($order->items as $item)
                <div class="flex justify-between text-xs">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $item->product->name ?? '—' }}</p>
                        <p class="text-gray-400">{{ $item->qty }} x ₱{{ number_format($item->price, 2) }}</p>
                    </div>
                    <span class="font-bold text-gray-800">₱{{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
            </div>

            <div class="border-t border-dashed border-gray-300 mb-4"></div>

            {{-- Grand total --}}
            <div class="flex justify-between items-center mb-4">
                <span class="font-bold text-gray-700">TOTAL</span>
                <span class="text-xl font-black text-indigo-600">₱{{ number_format($order->grand_total, 2) }}</span>
            </div>

            <div class="border-t border-dashed border-gray-300 mb-4"></div>

            {{-- Footer --}}
            <div class="text-center">
                <p class="text-xs text-gray-400">Thank you for your purchase!</p>
                <p class="text-xs text-gray-300 mt-0.5">Powered by EasyVend</p>
            </div>
        </div>

        {{-- Print button --}}
        <div class="px-5 py-4 border-t border-gray-100 flex gap-3">
            <button onclick="printReceipt()"
                    class="flex-1 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all">
                🖨 Print
            </button>
            <button onclick="document.getElementById('receipt-modal').classList.add('hidden')"
                    class="flex-1 py-2.5 border border-gray-200 text-gray-500 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all">
                Close
            </button>
        </div>
    </div>
</div>

{{-- Print styles + script --}}
<style>
@media print {
    body * { visibility: hidden; }
    #receipt-content, #receipt-content * { visibility: visible; }
    #receipt-content {
        position: fixed;
        top: 0; left: 0;
        width: 80mm;
        font-size: 12px;
        padding: 10mm;
    }
}
</style>

<script>
function printReceipt() {
    window.print();
}
</script>
@endif

@endsection