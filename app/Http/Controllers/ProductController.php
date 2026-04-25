<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
        public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $filter  = $request->get('filter', '');

        $query = Product::when($search, fn($q) =>
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
        );

        if ($filter === 'out') {
            $query->where('stock', 0);
        } elseif ($filter === 'low') {
            $query->where('stock', '>', 0)->where('stock', '<=', 10);
        } elseif ($filter === 'expired') {
            $query->whereNotNull('expiry_date')->whereDate('expiry_date', '<', today());
        } elseif ($filter === 'expiring') {
            $query->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', today())
                ->whereDate('expiry_date', '<=', today()->addDays(30));
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        $expiredCount      = Product::whereNotNull('expiry_date')
                                ->whereDate('expiry_date', '<', today())->count();
        $expiringSoonCount = Product::whereNotNull('expiry_date')
                                ->whereDate('expiry_date', '>=', today())
                                ->whereDate('expiry_date', '<=', today()->addDays(30))->count();

        return view('products.index', compact(
            'products', 'search', 'filter',
            'expiredCount', 'expiringSoonCount'
        ));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'category'    => 'required|string|max:80',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $existing = Product::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();
        $data = $request->only('name', 'category', 'price', 'stock', 'expiry_date');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
            }

        Product::create($data);

              return redirect()->route('products.index')
                     ->with('success', 'Product added successfully.');

        if ($existing) {
            $existing->increment('stock', $request->stock);
            if ($request->expiry_date) {
                if (!$existing->expiry_date || $request->expiry_date < $existing->expiry_date->format('Y-m-d')) {
                    $existing->update(['expiry_date' => $request->expiry_date]);
                }
            }
            $newStock = $existing->fresh()->stock;
            return redirect()->route('products.index')
                             ->with('success', "'{$existing->name}' already exists. Stock updated by +{$request->stock} (now {$newStock}).");
        }

        Product::create($request->only('name', 'category', 'price', 'stock', 'expiry_date'));

        return redirect()->route('products.index')
                         ->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:150|unique:products,name,' . $product->id,
            'category'    => 'required|string|max:80',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('name', 'category', 'price', 'stock', 'expiry_date');

        // Handle image removal
        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $data['image'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image first
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')
                        ->with('success', 'Product updated successfully.');
    }

        // ── Export products as PDF ─────────────────────
        public function export(Request $request)
    {
        $search = $request->get('search', '');
        $filter = $request->get('filter', '');

        $query = Product::when($search, fn($q) =>
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
        );

        if ($filter === 'out') {
            $query->where('stock', 0);
        } elseif ($filter === 'low') {
            $query->where('stock', '>', 0)->where('stock', '<=', 10);
        } elseif ($filter === 'expired') {
            $query->whereNotNull('expiry_date')->whereDate('expiry_date', '<', today());
        } elseif ($filter === 'expiring') {
            $query->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', today())
                ->whereDate('expiry_date', '<=', today()->addDays(30));
        }

        $products = $query->latest()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('products.pdf', [
            'products'    => $products,
            'search'      => $search,
            'filter'      => $filter,
            'generatedAt' => now()->format('M d, Y h:i A'),
            'generatedBy' => auth()->user()->name,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('easyvend-products-' . now()->format('Y-m-d') . '.pdf');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
                         ->with('success', 'Product deleted.');
    }
}