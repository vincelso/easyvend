<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // READ
    public function index(Request $request)
    {
        $user   = auth()->user();
        $search = $request->get('search', '');

        $query = Transaction::with(['product', 'user']);

        if ($user->isCashier()) {
            $query->where('user_id', $user->id);
        }

        if ($search) {
            $query->whereHas('product', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            );
        }

        $transactions = $query->latest()->paginate(8)->withQueryString();

        return view('transactions.index', compact('transactions', 'search'));
    }

    // CREATE form
    public function create()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('transactions.create', compact('products'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'qty'            => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->qty > $product->stock) {
            return back()
                ->withErrors(['qty' => "Only {$product->stock} items in stock."])
                ->withInput();
        }

        Transaction::create([
            'user_id'        => auth()->id(),
            'product_id'     => $product->id,
            'qty'            => $request->qty,
            'price'          => $product->price,
            'total'          => $product->price * $request->qty,
            'payment_method' => $request->payment_method,
        ]);

        $product->decrement('stock', $request->qty);

        return redirect()->route('transactions.index')
                         ->with('success', 'Transaction recorded successfully.');
    }

    // SHOW
    public function show(Transaction $transaction)
    {
        $this->gate($transaction);
        return view('transactions.show', compact('transaction'));
    }

    // EDIT form
    public function edit(Transaction $transaction)
    {
        $this->gate($transaction);
        $products = Product::orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'products'));
    }

    // UPDATE
    public function update(Request $request, Transaction $transaction)
    {
        $this->gate($transaction);

        $request->validate([
            'product_id'     => 'required|exists:products,id',
            'qty'            => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Restore old stock first
        $transaction->product->increment('stock', $transaction->qty);

        if ($request->qty > $product->fresh()->stock) {
            $transaction->product->decrement('stock', $transaction->qty);
            return back()
                ->withErrors(['qty' => "Only {$product->stock} items in stock."])
                ->withInput();
        }

        $product->decrement('stock', $request->qty);

        $transaction->update([
            'product_id'     => $product->id,
            'qty'            => $request->qty,
            'price'          => $product->price,
            'total'          => $product->price * $request->qty,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('transactions.index')
                         ->with('success', 'Transaction updated successfully.');
    }

    // DELETE
    public function destroy(Transaction $transaction)
    {
        $this->gate($transaction);

        $transaction->product->increment('stock', $transaction->qty);
        $transaction->delete();

        return redirect()->route('transactions.index')
                         ->with('success', 'Transaction deleted.');
    }

    // ── Authorization helper ──────────────────────
    private function gate(Transaction $transaction): void
    {
        if (auth()->user()->isCashier() && $transaction->user_id !== auth()->id()) {
            abort(403, 'You can only manage your own transactions.');
        }
    }
}