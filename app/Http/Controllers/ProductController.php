<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class ProductController extends Controller
{
    private function getCloudinary()
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true]
        ]);
        return new Cloudinary();
    }

    public function index(Request $request)
    {
        $search   = $request->get('search', '');
        $filter   = $request->get('filter', '');
        $category = $request->get('category', '');

        $query = Product::when($search, fn($q) =>
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
        );

        if ($category) {
            $query->where('category', $category);
        }

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
        $categories = Product::distinct()->orderBy('category')->pluck('category');

        $expiredCount      = Product::whereNotNull('expiry_date')
                                ->whereDate('expiry_date', '<', today())->count();
        $expiringSoonCount = Product::whereNotNull('expiry_date')
                                ->whereDate('expiry_date', '>=', today())
                                ->whereDate('expiry_date', '<=', today()->addDays(30))->count();

        return view('products.index', compact(
            'products', 'search', 'filter', 'category',
            'categories', 'expiredCount', 'expiringSoonCount'
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
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $existing = Product::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();

        if ($existing) {
            $existing->increment('stock', $request->stock);
            if ($request->hasFile('image')) {
                if ($existing->image_public_id) {
                    $this->getCloudinary()->uploadApi()->destroy($existing->image_public_id);
                }
                $uploaded = $this->getCloudinary()->uploadApi()->upload(
                    $request->file('image')->getRealPath(),
                    ['folder' => 'easyvend/products']
                );
                $existing->update([
                    'image'           => $uploaded['secure_url'],
                    'image_public_id' => $uploaded['public_id'],
                ]);
            }
            $newStock = $existing->fresh()->stock;
            return redirect()->route('products.index')
                ->with('success', "'{$existing->name}' already exists. Stock updated by +{$request->stock} (now {$newStock}).");
        }

        $data = $request->only('name', 'category', 'price', 'stock', 'expiry_date');

        if ($request->hasFile('image')) {
            $uploaded = $this->getCloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'easyvend/products']
            );
            $data['image']           = $uploaded['secure_url'];
            $data['image_public_id'] = $uploaded['public_id'];
        }

        Product::create($data);

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
            $this->getCloudinary()->uploadApi()->destroy($product->image_public_id);
            $data['image']           = null;
            $data['image_public_id'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            if ($product->image_public_id) {
                $this->getCloudinary()->uploadApi()->destroy($product->image_public_id);
            }
            $uploaded = $this->getCloudinary()->uploadApi()->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'easyvend/products']
            );
            $data['image']           = $uploaded['secure_url'];
            $data['image_public_id'] = $uploaded['public_id'];
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function export(Request $request)
    {
        $search   = $request->get('search', '');
        $filter   = $request->get('filter', '');
        $category = $request->get('category', '');

        $query = Product::when($search, fn($q) =>
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
        );

        if ($category) {
            $query->where('category', $category);
        }

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
            'category'    => $category,
            'generatedAt' => now()->format('M d, Y h:i A'),
            'generatedBy' => auth()->user()->name,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('easyvend-products-' . now()->format('Y-m-d') . '.pdf');
    }

    public function destroy(Product $product)
    {
        if ($product->image_public_id) {
            $this->getCloudinary()->uploadApi()->destroy($product->image_public_id);
        }
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Product deleted.');
    }
}