<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            abort(403);
        }

        // Supplier gets their own catalog view
        if (auth()->check() && auth()->user()->isSupplier()) {
            $supplierId = auth()->user()->supplier_id;
            $products   = Product::with('category')
                ->where('supplier_id', $supplierId)
                ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->integer('category_id')))
                ->latest()
                ->paginate(12)
                ->withQueryString();

            $categories = Category::whereHas('products', fn($q) => $q->where('supplier_id', $supplierId))
                ->orderBy('name')
                ->get();

            return view('products.catalog', compact('products', 'categories'));
        }

        $products = Product::with(['category', 'supplier'])
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->when($request->filled('supplier_id'), function ($query) use ($request) {
                $query->where('supplier_id', $request->integer('supplier_id'));
            })
            ->when($request->query('filter') === 'low_stock', function ($query) {
                $query->whereColumn('stock_quantity', '<=', 'reorder_level');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name', 'asc')->get(),
            'suppliers' => Supplier::query()->orderBy('name', 'asc')->get(),
            'selectedCategoryId' => $request->integer('category_id'),
            'selectedSupplierId' => $request->integer('supplier_id'),
            'selectedFilter' => $request->query('filter'),
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated + [
            'unit' => 'pcs',
        ]);

        if (! empty($product->supplier_id)) {
            return redirect()
                ->route('stock-requests.create', $product)
                ->with('status', 'Product created. Now request stock for this company.');
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully. Assign a company to request stock.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            abort(403);
        }

        $product->load(['category', 'supplier']);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->stock_quantity > 0 || !empty($product->supplier_id)) {
            return redirect()->route('products.index')->with('error', 'Cannot delete product: It currently has stock or is bound to a supplier.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('status', 'Product deleted successfully.');
    }
}