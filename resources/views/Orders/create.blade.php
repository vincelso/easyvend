    @extends('layouts.app')
    @section('title', 'New Order — POS')

    @section('content')
    <div class="flex gap-4 h-[calc(100vh-120px)]">

        {{-- LEFT: Product Catalog --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Category tabs + Search --}}
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                <input type="text" id="product-search" placeholder="Search product..."
                    oninput="filterProducts()"
                    class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 w-48 bg-white">
                <div class="flex gap-1 flex-wrap" id="category-tabs">
                    <button onclick="filterCategory('all')" data-cat="all"
                            class="cat-tab px-3 py-1.5 text-xs font-semibold rounded-xl border transition-all bg-indigo-600 text-white border-indigo-600">
                        All
                    </button>
                    @foreach($categories as $cat)
                    <button onclick="filterCategory('{{ $cat }}')" data-cat="{{ $cat }}"
                            class="cat-tab px-3 py-1.5 text-xs font-semibold rounded-xl border transition-all bg-white text-gray-600 border-gray-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300">
                        {{ $cat }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="overflow-y-auto flex-1 pr-1">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3" id="product-grid">
                    @foreach($products as $product)
                    <button type="button"
                            onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }})"
                            data-name="{{ strtolower($product->name) }}"
                            data-category="{{ $product->category }}"
                            data-stock="{{ $product->stock }}"
                            class="product-card bg-white rounded-2xl border border-gray-200 shadow-sm p-3 text-left hover:border-indigo-400 hover:shadow-md transition-all active:scale-95 group">
                        {{-- Image --}}
                        <div class="w-full aspect-square rounded-xl overflow-hidden mb-2 bg-indigo-50 flex items-center justify-center">
                            @if($product->hasImage())
                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">
                            @else
                            <span class="text-indigo-600 font-black text-2xl uppercase">{{ substr($product->name, 0, 2) }}</span>
                            @endif
                        </div>
                        {{-- Info --}}
                        <p class="text-xs font-bold text-gray-800 leading-tight line-clamp-2 mb-1">{{ $product->name }}</p>
                        <p class="text-sm font-black text-indigo-600">₱{{ number_format($product->price, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $product->stock }} left</p>
                    </button>
                    @endforeach
                </div>
                <p id="no-results" class="hidden text-center text-gray-400 text-sm py-10">No products found.</p>
            </div>
        </div>

        {{-- RIGHT: Cart --}}
        <div class="w-80 flex flex-col bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex-shrink-0">

            {{-- Cart Header --}}
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <h2 class="font-bold text-gray-900 text-sm">🛒 Current Sale</h2>
            </div>

            {{-- Customer + Payment --}}
            <div class="px-4 py-3 border-b border-gray-100 space-y-2">
                <input type="text" id="customer-name" value="Walk-in Customer"
                    placeholder="Customer name"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <select id="payment-method"
                        onchange="toggleCashCalculator()"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    @foreach(['Cash', 'GCash', 'Maya', 'Card'] as $m)
                    <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto px-4 py-3 space-y-2" id="cart-list">
                <p class="text-center text-gray-400 text-xs py-6">No items yet. Tap a product to add.</p>
            </div>

            {{-- Totals + Cash Calculator --}}
            <div class="border-t border-gray-100 px-4 py-3 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-700">Grand Total</span>
                    <span id="grand-total" class="text-xl font-black text-indigo-600">₱0.00</span>
                </div>

                {{-- Cash Calculator --}}
                <div id="cash-section" class="space-y-2 pt-2 border-t border-dashed border-gray-200">
                    <input type="number" id="cash-tendered" placeholder="Cash received..."
                        oninput="computeChange()" step="0.01" min="0"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-300">
                    <div class="grid grid-cols-3 gap-1">
                        @foreach([20, 50, 100, 200, 500, 1000] as $amount)
                        <button type="button" onclick="setTendered({{ $amount }})"
                                class="py-1 text-xs font-semibold border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 transition-all">
                            ₱{{ $amount }}
                        </button>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Change</span>
                        <span id="change-display" class="font-black text-green-600 text-base">₱0.00</span>
                    </div>
                    <p id="cash-error" class="text-xs text-red-500 hidden">⚠ Amount is less than total.</p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="px-4 pb-4">
                <button onclick="submitOrder()"
                        class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all text-sm shadow-sm">
                    ✓ Place Sale
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden form --}}
    <form method="POST" action="{{ route('orders.store') }}" id="order-form">
        @csrf
        <input type="hidden" name="customer_name" id="form-customer">
        <input type="hidden" name="payment_method" id="form-payment">
        <input type="hidden" name="amount_tendered" id="form-tendered" value="0"> {{-- ADD THIS --}}
        <div id="form-items"></div>
    </form>

    <script>
const products = @json($products->keyBy('id'));
let cart = {};

function addToCart(id, name, price, stock) {
    id = String(id);
    if (cart[id]) {
        if (cart[id].qty >= stock) {
            alert(`Only ${stock} left in stock for ${name}.`);
            return;
        }
        cart[id].qty++;
    } else {
        cart[id] = { name, price, qty: 1, stock };
    }
    renderCart();
}

function changeQty(id, delta) {
    id = String(id);
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) {
        delete cart[id];
    } else if (cart[id].qty > cart[id].stock) {
        cart[id].qty = cart[id].stock;
        alert(`Only ${cart[id].stock} left in stock.`);
    }
    renderCart();
}

function removeFromCart(id) {
    id = String(id);
    delete cart[id];
    renderCart();
}

function setQty(id, val) {
    id = String(id);
    const qty = parseInt(val) || 1;
    if (!cart[id]) return;
    if (qty <= 0) {
        delete cart[id];
    } else if (qty > cart[id].stock) {
        cart[id].qty = cart[id].stock;
        alert(`Only ${cart[id].stock} left in stock.`);
    } else {
        cart[id].qty = qty;
    }
    renderCart();
}

function renderCart() {
    const list = document.getElementById('cart-list');
    const keys = Object.keys(cart);

    if (keys.length === 0) {
        list.innerHTML = '<p class="text-center text-gray-400 text-xs py-6">No items yet. Tap a product to add.</p>';
        updateTotal();
        return;
    }

        list.innerHTML = keys.map(id => {
        const item = cart[id];
        const sub  = (item.price * item.qty).toFixed(2);
        return `
        <div class="flex items-center gap-2 py-2 border-b border-gray-50 last:border-0">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">${item.name}</p>
                <p class="text-xs text-gray-400">₱${item.price.toFixed(2)} each</p>
            </div>
            <div class="flex items-center gap-1 flex-shrink-0">
                <button onclick="changeQty('${id}', -1)"
                        class="w-6 h-6 rounded-md bg-gray-100 text-gray-600 hover:bg-red-100 hover:text-red-600 text-sm font-bold flex items-center justify-center transition-all">−</button>
                <input type="text" inputmode="numeric" pattern="[0-9]*" value="${item.qty}"
       onchange="setQty('${id}', this.value)"
       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
       style="width:36px; height:24px; text-align:center; font-size:11px; font-weight:bold; border:1px solid #e5e7eb; border-radius:6px; background:white; outline:none;">
                <button onclick="changeQty('${id}', 1)"
                        class="w-6 h-6 rounded-md bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-600 text-sm font-bold flex items-center justify-center transition-all">+</button>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xs font-bold text-indigo-600">₱${parseFloat(sub).toLocaleString('en-PH', {minimumFractionDigits:2})}</p>
                <button onclick="removeFromCart('${id}')" class="text-xs text-red-400 hover:text-red-600">remove</button>
            </div>
        </div>`;
    }).join('');

    updateTotal();
}

function updateTotal() {
    let total = 0;
    Object.values(cart).forEach(i => total += i.price * i.qty);
    document.getElementById('grand-total').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits:2});
    computeChange();
}

function computeChange() {
    let total = 0;
    Object.values(cart).forEach(i => total += i.price * i.qty);
    const tendered = parseFloat(document.getElementById('cash-tendered').value) || 0;
    const change   = tendered - total;
    const display  = document.getElementById('change-display');
    const error    = document.getElementById('cash-error');

    if (tendered > 0 && change < 0) {
        display.textContent = '₱0.00';
        error.classList.remove('hidden');
    } else {
        error.classList.add('hidden');
        display.textContent = '₱' + Math.max(0, change).toLocaleString('en-PH', {minimumFractionDigits:2});
    }
}

function setTendered(amount) {
    document.getElementById('cash-tendered').value = amount;
    computeChange();
}

function toggleCashCalculator() {
    const method  = document.getElementById('payment-method').value;
    const section = document.getElementById('cash-section');
    section.style.display = method === 'Cash' ? 'block' : 'none';
}

function filterCategory(cat) {
    document.querySelectorAll('.cat-tab').forEach(btn => {
        const active = btn.dataset.cat === cat;
        btn.className = `cat-tab px-3 py-1.5 text-xs font-semibold rounded-xl border transition-all ${
            active ? 'bg-indigo-600 text-white border-indigo-600'
                   : 'bg-white text-gray-600 border-gray-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-300'
        }`;
    });
    const search = document.getElementById('product-search').value.toLowerCase();
    filterProducts(cat, search);
}

function filterProducts(cat, search) {
    if (cat === undefined) cat = document.querySelector('.cat-tab.bg-indigo-600')?.dataset.cat || 'all';
    if (search === undefined) search = document.getElementById('product-search').value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('.product-card').forEach(card => {
        const matchCat    = cat === 'all' || card.dataset.category === cat;
        const matchSearch = card.dataset.name.includes(search);
        const show        = matchCat && matchSearch;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('no-results').classList.toggle('hidden', visible > 0);
}

function submitOrder() {
    const keys = Object.keys(cart);
    if (keys.length === 0) {
        alert('Please add at least one product to the sale.');
        return;
    }

    const payment = document.getElementById('payment-method').value;

    if (payment === 'Cash') {
        let total = 0;
        Object.values(cart).forEach(i => total += i.price * i.qty);
        const tendered = parseFloat(document.getElementById('cash-tendered').value) || 0;
        if (tendered < total) {
            alert('⚠ Please enter a cash amount that covers the total of ₱' + total.toFixed(2));
            return;
        }
    }

    if (!confirm('Place this sale?')) return;

    document.getElementById('form-customer').value  = document.getElementById('customer-name').value || 'Walk-in Customer';
    document.getElementById('form-payment').value   = payment;
    document.getElementById('form-tendered').value  = payment === 'Cash'
        ? (parseFloat(document.getElementById('cash-tendered').value) || 0)
        : 0;

    const container = document.getElementById('form-items');
    container.innerHTML = '';
    keys.forEach((id, i) => {
        container.innerHTML += `
            <input type="hidden" name="items[${i}][product_id]" value="${id}">
            <input type="hidden" name="items[${i}][qty]" value="${cart[id].qty}">
        `;
    });

    document.getElementById('order-form').submit();
}

// Init
toggleCashCalculator();
</script>
    @endsection