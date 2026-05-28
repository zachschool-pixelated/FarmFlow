<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockRequestController extends Controller
{
    public function create(Product $product)
    {
        $user = Auth::user();

        if ($user->role !== 'manager') {
            abort(403);
        }

        if (! $product->supplier_id) {
            return redirect()->route('products.index')->with('error', 'This product does not have a company assigned yet.');
        }

        return view('stock-requests.create', [
            'product' => $product->load(['category', 'supplier']),
        ]);
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            abort(403);
        }

        if (($user->role ?? null) === 'supplier') {
            $statusFilter = request()->input('status');

            $query = StockRequest::with(['product', 'user'])
                ->where('supplier_id', $user->supplier_id);

            if ($statusFilter && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $requests = $query->latest()->paginate(15)->withQueryString();

            // Counts for each filter tab
            $statusCounts = StockRequest::where('supplier_id', $user->supplier_id)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            return view('stock-requests.index', compact('requests', 'statusFilter', 'statusCounts'));
        }

        // Admin sees all requests
        $requests = StockRequest::with(['product', 'supplier', 'user'])
            ->when(request()->filled('supplier_id'), function ($query) {
                $query->where('supplier_id', request()->integer('supplier_id'));
            })
            ->latest()
            ->paginate(15);

        $statusFilter = null;
        $statusCounts = collect();

        return view('stock-requests.index', compact('requests', 'statusFilter', 'statusCounts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'manager') abort(403);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_requested' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->supplier_id) {
            return back()->with('error', 'Product has no assigned supplier.');
        }

        StockRequest::create([
            'product_id' => $product->id,
            'supplier_id' => $product->supplier_id,
            'user_id' => $user->id,
            'quantity_requested' => $validated['quantity_requested'],
            'status' => 'pending',
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('stock-requests.index')->with('status', 'Stock request sent to supplier.');
    }

    public function show(StockRequest $stockRequest)
    {
        $user = Auth::user();

        if ($user->role === 'admin') abort(403);

        // Ensure supplier can only view their own requests
        if (($user->role ?? null) === 'supplier' && $stockRequest->supplier_id !== $user->supplier_id) {
            abort(403);
        }

        $stockRequest->load(['product.category', 'user', 'supplier']);

        return view('stock-requests.show', compact('stockRequest'));
    }

    public function update(Request $request, StockRequest $stockRequest)
    {
        $user = Auth::user();
        if ($user->role === 'admin') abort(403);

        $validated = $request->validate([
            'status'               => 'required|in:pending,processing,shipped,completed,rejected',
            'expected_delivery_at' => 'nullable|date|required_if:status,processing,shipped',
        ]);

        $newStatus = $validated['status'];

        if (($user->role ?? null) === 'supplier') {
            if ($stockRequest->supplier_id !== $user->supplier_id) abort(403);
            if (in_array($newStatus, ['completed'])) abort(403, 'Suppliers cannot mark as completed.');

            if ($newStatus === 'processing' && $stockRequest->status !== 'processing') {
                $stockRequest->update([
                    'status'               => 'processing',
                    'expected_delivery_at' => $validated['expected_delivery_at'] ?? $stockRequest->expected_delivery_at,
                ]);
                return back()->with('status', 'Request acknowledged. ETA provided.');
            }

            // When supplier marks as shipped, record timestamps
            if ($newStatus === 'shipped' && $stockRequest->status !== 'shipped') {
                $stockRequest->update([
                    'status'               => 'shipped',
                    'shipped_at'           => now(),
                    'expected_delivery_at' => $validated['expected_delivery_at'] ?? $stockRequest->expected_delivery_at,
                ]);
                return back()->with('status', 'Request marked as shipped.');
            }
        }

        if ($user->role === 'manager') {
            // Manager can mark as completed
            if ($newStatus === 'completed' && $stockRequest->status !== 'completed') {
                // Fulfill the request and create a Stock In
                $userId = $user->id;

                DB::transaction(function () use ($stockRequest, $userId) {
                    $stockRequest->update(['status' => 'completed']);
                    
                    $product = $stockRequest->product;
                    $stockBefore = $product->stock_quantity;
                    $stockAfter = $stockBefore + $stockRequest->quantity_requested;

                    $product->update(['stock_quantity' => $stockAfter]);

                    StockMovement::create([
                        'product_id'   => $product->id,
                        'user_id'      => $userId,
                        'type'         => 'in',
                        'quantity'     => $stockRequest->quantity_requested,
                        'stock_before' => $stockBefore,
                        'stock_after'  => $stockAfter,
                        'reference'    => 'Stock Request #' . $stockRequest->id,
                        'reason'       => 'Automated stock-in from supplier fulfillment.',
                        'notes'        => 'Automated stock-in from supplier fulfillment.',
                    ]);
                });

                return back()->with('status', 'Stock request completed and inventory updated.');
            }
        }

        $stockRequest->update(['status' => $newStatus]);
        return back()->with('status', 'Stock request status updated.');
    }
}
