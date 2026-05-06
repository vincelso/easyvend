<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ── POS / New Order page ──────────────────────
    public function create()
    {
        $products   = Product::where('stock', '>', 0)->orderBy('name')->get();
        $categories = Product::where('stock', '>', 0)->distinct()->orderBy('category')->pluck('category'); // ← ADD
        $openOrders = Order::with('items.product')
                            ->where('user_id', auth()->id())
                            ->where('status', 'open')
                            ->latest()->take(10)->get();

        if (auth()->user()->isAdmin()) {
            $openOrders = Order::with(['items.product', 'user'])
                            ->where('status', 'open')
                            ->latest()->take(10)->get();
        }

        return view('orders.create', compact('products', 'openOrders', 'categories')); // ← add categories
    }

    // ── Start a new order (save customer name + payment) ──
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'payment_method' => 'required|string',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        // Create the order
        $order = Order::create([
            'user_id'         => auth()->id(),
            'customer_name'   => $request->customer_name,
            'payment_method'  => $request->payment_method,
            'status'          => 'open',
            'grand_total'     => 0,
            'amount_tendered' => $request->payment_method === 'Cash' ? $request->amount_tendered : null, // ← ADD
        ]);

        // Add each item
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($item['qty'] > $product->stock) {
                $order->delete();
                return back()
                    ->withErrors(['items' => "Not enough stock for {$product->name}. Only {$product->stock} left."])
                    ->withInput();
            }

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'qty'        => $item['qty'],
                'price'      => $product->price,
                'subtotal'   => $product->price * $item['qty'],
            ]);

            $product->decrement('stock', $item['qty']);
        }

        $order->recalculateTotal();

        return redirect()->route('orders.show', $order)
                         ->with('success', 'Sale created successfully.');
    }

    // ── View order (receipt style) ─────────────────
    public function show(Order $order)
    {
        $this->gateOrder($order);
        $order->load('items.product', 'user');
        $products   = Product::where('stock', '>', 0)->orderBy('name')->get();
        $categories = Product::where('stock', '>', 0)->distinct()->orderBy('category')->pluck('category'); // ← ADD
        return view('orders.show', compact('order', 'products', 'categories')); // ← add categories
    }

    // ── Add more items to an existing OPEN order ───
    public function addItem(Request $request, Order $order)
    {
        $this->gateOrder($order);

        if (!$order->isOpen()) {
            return back()->with('error', 'This sale is already completed and cannot be modified.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->qty > $product->stock) {
            return back()->withErrors(['qty' => "Only {$product->stock} left in stock."]);
        }

        // If same product already in order, update qty instead of adding duplicate
        $existing = $order->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->update([
                'qty'      => $existing->qty + $request->qty,
                'subtotal' => $product->price * ($existing->qty + $request->qty),
            ]);
        } else {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'qty'        => $request->qty,
                'price'      => $product->price,
                'subtotal'   => $product->price * $request->qty,
            ]);
        }

        $product->decrement('stock', $request->qty);
        $order->recalculateTotal();

        return back()->with('success', "Added {$request->qty}x {$product->name} to the sale.");
    }

    // ── Remove item from sale ─────────────────────
    public function removeItem(Order $order, OrderItem $item)
    {
        $this->gateOrder($order);

        if (!$order->isOpen()) {
            return back()->with('error', 'This sale is already completed.');
        }

        // Restore stock
        $item->product->increment('stock', $item->qty);
        $item->delete();
        $order->recalculateTotal();

        return back()->with('success', 'Item removed from sale.');
    }

    public function complete(Request $request, Order $order)
{
    $this->gateOrder($order);

    if ($order->items->isEmpty()) {
        return back()->with('error', 'Cannot complete an empty sale.');
    }

    // Save tendered amount for Cash payments
    if ($order->payment_method === 'Cash' && $request->amount_tendered) {
        $order->update(['amount_tendered' => $request->amount_tendered]);
    }

    // Auto-create a transaction record for each item
    foreach ($order->items as $item) {
        Transaction::create([
            'user_id'        => $order->user_id,
            'product_id'     => $item->product_id,
            'qty'            => $item->qty,
            'price'          => $item->price,
            'total'          => $item->subtotal,
            'payment_method' => $order->payment_method,
        ]);
    }

    $order->update(['status' => 'completed']);

    return redirect()->route('orders.index')
                     ->with('success', "Sale #{$order->id} for {$order->customer_name} completed.");
}

        // Update payment method
    public function updatePayment(Request $request, Order $order)
    {
        $this->gateOrder($order);

        if (!$order->isOpen()) {
            return back()->with('error', 'Cannot modify a completed sale.');
        }

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $order->update(['payment_method' => $request->payment_method]);

        return back()->with('success', 'Payment method updated.');
    }

    // Update item quantity (+/-)
    public function updateItemQty(Request $request, Order $order, OrderItem $item)
    {
        $this->gateOrder($order);

        if (!$order->isOpen()) {
            return back()->with('error', 'Cannot modify a completed sale.');
        }

        $request->validate(['qty' => 'required|integer|min:0']);

        if ($request->qty == 0) {
            // Restore stock and remove item
            $item->product->increment('stock', $item->qty);
            $item->delete();
        } else {
            $diff = $request->qty - $item->qty;

            if ($diff > 0 && $diff > $item->product->stock) {
                return back()->withErrors(['qty' => "Only {$item->product->stock} left in stock."]);
            }

            // Adjust stock based on difference
            if ($diff > 0) {
                $item->product->decrement('stock', $diff);
            } elseif ($diff < 0) {
                $item->product->increment('stock', abs($diff));
            }

            $item->update([
                'qty'      => $request->qty,
                'subtotal' => $item->price * $request->qty,
            ]);
        }

        $order->recalculateTotal();

        return back()->with('success', 'Quantity updated.');
    }

    // ── List all orders ────────────────────────────
        public function index(Request $request)
    {
        $search   = $request->get('search', '');
        $status   = $request->get('status', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');

        $query = Order::with(['items', 'user']);

        if (auth()->user()->isCashier()) {
            $query->where('user_id', auth()->id());
        }
        if ($search) {
            $query->where('customer_name', 'like', "%{$search}%");
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('orders.index', compact('orders', 'search', 'status', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $search   = $request->get('search', '');
        $status   = $request->get('status', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo   = $request->get('date_to', '');
        $user     = auth()->user();

        $query = Order::with(['items', 'user']);

        if ($user->isCashier()) {
            $query->where('user_id', $user->id);
        }
        if ($search) {
            $query->where('customer_name', 'like', "%{$search}%");
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->latest()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('orders.pdf', [
            'orders'      => $orders,
            'search'      => $search,
            'status'      => $status,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'generatedAt' => now()->format('M d, Y h:i A'),
            'generatedBy' => $user->name,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('easyvend-sales-' . now()->format('Y-m-d') . '.pdf');
    }

    

    // ── Delete order ───────────────────────────────
    public function destroy(Order $order)
    {
        $this->gateOrder($order);

        // Restore stock for all items
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->qty);
        }

        $order->delete();

        return redirect()->route('orders.index')
                         ->with('success', 'Sale deleted and stock restored.');
    }

    // ── Authorization ──────────────────────────────
    private function gateOrder(Order $order): void
    {
        if (auth()->user()->isCashier() && $order->user_id !== auth()->id()) {
            abort(403, 'You can only manage your own sales.');
        }
    }
}