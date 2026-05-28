<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $movements = StockMovement::with(['product', 'user'])
            ->when($request->filled('product_id'), function ($query) use ($request) {
                $query->where('product_id', $request->integer('product_id'));
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock-movements.index', [
            'movements' => $movements,
            'products' => Product::orderBy('name')->get(),
            'selectedProductId' => $request->integer('product_id'),
            'selectedType' => $request->input('type'),
        ]);
    }

    public function create(): View
    {
        return view('stock-movements.create', [
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if stock-out exceeds available stock
        if ($validated['type'] === 'out' && $validated['quantity'] > $product->stock_quantity) {
            return back()->withErrors([
                'quantity' => 'Cannot remove more than the available stock ('.$product->stock_quantity.' units).',
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $product) {
            $stockBefore = $product->stock_quantity;
            $reason = trim((string) ($validated['notes'] ?? ''));

            if ($reason === '') {
                $reason = $validated['type'] === 'in'
                    ? 'Stock added'
                    : 'Stock removed';
            }

            if ($validated['type'] === 'in') {
                $product->increment('stock_quantity', $validated['quantity']);
            } else {
                $product->decrement('stock_quantity', $validated['quantity']);
            }

            StockMovement::create([
                'product_id' => $validated['product_id'],
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $product->fresh()->stock_quantity,
                'reason' => $reason,
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('stock-movements.index')->with('status', 'Stock movement recorded successfully.');
    }

    public function show(StockMovement $stockMovement): View
    {
        return view('stock-movements.show', [
            'movement' => $stockMovement->load(['product', 'user']),
        ]);
    }

    public function void(Request $request, StockMovement $stockMovement): RedirectResponse
    {
        $validated = $request->validate([
            'void_reason' => 'required|string|max:255',
        ]);

        if ($stockMovement->voided_at) {
            return redirect()->route('stock-movements.index')->with('error', 'This movement is already voided.');
        }

        $product = $stockMovement->product;

        // Check if voiding an 'in' movement would cause negative stock
        if ($stockMovement->type === 'in' && $stockMovement->quantity > $product->stock_quantity) {
            return redirect()->route('stock-movements.index')->with('error', 'Cannot void this transaction. The resulting stock would be negative.');
        }

        // Create inverse transaction
        DB::transaction(function () use ($stockMovement, $product, $validated) {
            $stockMovement->update([
                'voided_at' => now(),
                'void_reason' => $validated['void_reason'],
            ]);

            $stockBefore = $product->stock_quantity;
            $inverseType = $stockMovement->type === 'in' ? 'out' : 'in';

            if ($inverseType === 'in') {
                $product->increment('stock_quantity', $stockMovement->quantity);
            } else {
                $product->decrement('stock_quantity', $stockMovement->quantity);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $inverseType,
                'quantity' => $stockMovement->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $product->fresh()->stock_quantity,
                'reason' => 'Voiding movement #' . $stockMovement->id . ': ' . $validated['void_reason'],
                'reference' => 'VOID-' . $stockMovement->id,
            ]);
        });

        return redirect()->route('stock-movements.index')->with('status', 'Stock movement voided successfully.');
    }
}
