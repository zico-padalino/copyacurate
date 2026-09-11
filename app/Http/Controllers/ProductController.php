<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesDemoUser;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    use ResolvesDemoUser;

    public function index(): View
    {
        $user = $this->demoUser();
        $products = Product::query()->latest('id')->get();
        $stats = [
            'total' => $products->count(),
            'active' => $products->where('is_active', true)->count(),
            'low_stock' => $products->filter->isLowStock()->count(),
            'inventory_value' => $products->sum(fn (Product $product) => (float) $product->stock * (float) $product->buying_price),
        ];

        return view('products.index', compact('user', 'products', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('products'), 403);

        $request->merge([
            'selling_price' => $this->sanitizeMoney($request->input('selling_price')),
            'buying_price' => $this->sanitizeMoney($request->input('buying_price')),
            'stock' => $this->sanitizeMoney($request->input('stock')),
            'minimum_stock' => $this->sanitizeMoney($request->input('minimum_stock')),
        ]);

        $data = $request->validate([
            'sku' => ['required', 'string', 'max:40', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:product,service'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'buying_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Product::query()->create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function adjustStock(Request $request, Product $product): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('products'), 403);

        $request->merge(['quantity' => preg_replace('/[^\d\-]/', '', (string) $request->input('quantity'))]);
        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'not_in:0'],
        ]);

        $product->adjustStock((float) $data['quantity']);

        return redirect()->route('products.index')->with('success', 'Stok '.$product->sku.' berhasil disesuaikan.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        abort_unless($this->demoUser()->canManage('products'), 403);
        $product->update(['is_active' => ! $product->is_active]);

        return redirect()->route('products.index')->with('success', 'Status produk diperbarui.');
    }
}
