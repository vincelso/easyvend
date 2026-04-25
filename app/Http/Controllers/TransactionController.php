<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
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

        // ── Export transactions as PDF ─────────────────
    public function export(Request $request)
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

        $transactions = $query->latest()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transactions.pdf', [
            'transactions' => $transactions,
            'search'       => $search,
            'generatedAt'  => now()->format('M d, Y h:i A'),
            'generatedBy'  => $user->name,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('easyvend-transactions-' . now()->format('Y-m-d') . '.pdf');
    }

    public function destroy(Transaction $transaction)
    {
        $this->gate($transaction);

        // Note: we do NOT restore stock here since order already managed it
        $transaction->delete();

        return redirect()->route('transactions.index')
                         ->with('success', 'Transaction record deleted.');
    }

    private function gate(Transaction $transaction): void
    {
        if (auth()->user()->isCashier() && $transaction->user_id !== auth()->id()) {
            abort(403, 'You can only manage your own transactions.');
        }
    }
}