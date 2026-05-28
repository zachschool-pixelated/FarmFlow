<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductEditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductEditRequestController extends Controller
{
    /** Admin/Manager: list all edit requests */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'manager') abort(403);

        $status = $request->input('status');

        $editRequests = ProductEditRequest::with(['product', 'supplier', 'user', 'reviewer'])
            ->when($status && $status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusCounts = ProductEditRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('product-edit-requests.index', compact('editRequests', 'status', 'statusCounts'));
    }

    /** Supplier: show the Request Edit form for a product */
    public function create(Product $product)
    {
        $user = Auth::user();
        if (!$user->isSupplier()) abort(403);
        if ($product->supplier_id !== $user->supplier_id) abort(403);

        // Check if there's already a pending request
        $existingPending = ProductEditRequest::where('product_id', $product->id)
            ->where('status', 'pending')
            ->first();

        return view('product-edit-requests.create', compact('product', 'existingPending'));
    }

    /** Supplier: submit the edit request */
    public function store(Request $request, Product $product)
    {
        $user = Auth::user();
        if (!$user->isSupplier()) abort(403);
        if ($product->supplier_id !== $user->supplier_id) abort(403);

        // Block duplicate pending requests
        $alreadyPending = ProductEditRequest::where('product_id', $product->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return back()->with('error', 'This product already has a pending edit request.');
        }

        $validated = $request->validate([
            'reason'              => 'required|string|min:10|max:1000',
            'new_name'            => 'nullable|string|max:255',
            'new_description'     => 'nullable|string|max:2000',
            'new_price'           => 'nullable|numeric|min:0',
            'new_unit'            => 'nullable|string|max:50',
        ]);

        // Build only the fields that actually changed
        $changes = [];
        $fieldMap = [
            'new_name'        => 'name',
            'new_description' => 'description',
            'new_price'       => 'price',
            'new_unit'        => 'unit',
        ];
        foreach ($fieldMap as $inputKey => $field) {
            if (!empty($validated[$inputKey]) && $validated[$inputKey] != $product->$field) {
                $changes[$field] = $validated[$inputKey];
            }
        }

        if (empty($changes)) {
            return back()->with('error', 'No changes were detected. Please modify at least one field.');
        }

        ProductEditRequest::create([
            'product_id'        => $product->id,
            'supplier_id'       => $user->supplier_id,
            'user_id'           => $user->id,
            'status'            => 'pending',
            'reason'            => $validated['reason'],
            'requested_changes' => $changes,
        ]);

        return redirect()->route('products.index')
            ->with('status', 'Your edit request has been submitted and is awaiting review.');
    }

    /** Admin/Manager: view full request details */
    public function show(ProductEditRequest $productEditRequest)
    {
        $user = Auth::user();
        if ($user->role !== 'manager') abort(403);

        $productEditRequest->load(['product', 'supplier', 'user', 'reviewer']);
        return view('product-edit-requests.show', compact('productEditRequest'));
    }

    /** Admin/Manager: approve or reject */
    public function update(Request $request, ProductEditRequest $productEditRequest)
    {
        $user = Auth::user();
        if ($user->role !== 'manager') abort(403);

        if (!$productEditRequest->isPending()) {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $validated = $request->validate([
            'action'        => 'required|in:approve,reject',
            'reviewer_note' => 'nullable|string|max:1000|required_if:action,reject',
        ]);

        DB::transaction(function () use ($validated, $productEditRequest, $user) {
            if ($validated['action'] === 'approve') {
                // Apply the requested changes to the product
                $productEditRequest->product->update($productEditRequest->requested_changes);

                $productEditRequest->update([
                    'status'        => 'approved',
                    'reviewer_id'   => $user->id,
                    'reviewer_note' => $validated['reviewer_note'] ?? null,
                    'reviewed_at'   => now(),
                ]);
            } else {
                $productEditRequest->update([
                    'status'        => 'rejected',
                    'reviewer_id'   => $user->id,
                    'reviewer_note' => $validated['reviewer_note'],
                    'reviewed_at'   => now(),
                ]);
            }
        });

        $message = $validated['action'] === 'approve'
            ? 'Edit request approved. Product has been updated.'
            : 'Edit request rejected.';

        return redirect()->route('supplier-requests.index')->with('status', $message);
    }
}
