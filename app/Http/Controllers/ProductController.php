<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->get('search', '');
        $products = Product::when($search, fn($q) =>
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('category', 'like', "%{$search}%")
                    )->latest()->paginate(10)->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:150',
            'category' => 'required|string|max:80',
            'price'    => 'required|numeric|min:0',
            'stock'    => 'required|integer|min:0',
        ]);

        Product::create($request->only('name', 'category', 'price', 'stock'));

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
            'name'     => 'required|string|max:150',
            'category' => 'required|string|max:80',
            'price'    => 'required|numeric|min:0',
            'stock'    => 'required|integer|min:0',
        ]);

        $product->update($request->only('name', 'category', 'price', 'stock'));

        return redirect()->route('products.index')
                         ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
                         ->with('success', 'Product deleted.');
    }
}