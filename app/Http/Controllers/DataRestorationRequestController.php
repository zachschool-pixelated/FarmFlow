<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DataRestorationRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DataRestorationRequestController extends Controller
{
    // Managers view trash
    public function trash(): View
    {
        $products = Product::onlyTrashed()->with('category')->get();
        $categories = Category::onlyTrashed()->get();

        return view('trash.index', compact('products', 'categories'));
    }

    // Managers submit a request
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'model_type' => 'required|string|in:App\Models\Product,App\Models\Category',
            'model_id' => 'required|integer',
            'reason' => 'required|string|max:1000',
        ]);

        // Check if item actually exists in trash
        $modelClass = $validated['model_type'];
        $item = $modelClass::onlyTrashed()->find($validated['model_id']);

        if (!$item) {
            return redirect()->back()->withErrors(['model_id' => 'Item not found in trash.']);
        }

        DataRestorationRequest::create([
            'user_id' => Auth::id(),
            'model_type' => $validated['model_type'],
            'model_id' => $validated['model_id'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('trash.index')->with('status', 'Restoration request submitted to Administrators.');
    }

    // Admins view all requests
    public function index(): View
    {
        $pendingRequests = DataRestorationRequest::with('user', 'admin')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'pending_page');

        $historyRequests = DataRestorationRequest::with('user', 'admin')
            ->where('status', '!=', 'pending')
            ->orderByDesc('updated_at')
            ->paginate(15, ['*'], 'history_page');

        return view('data-restorations.index', compact('pendingRequests', 'historyRequests'));
    }

    public function show(DataRestorationRequest $dataRestoration): View
    {
        $dataRestoration->load('user', 'admin');
        
        $modelClass = $dataRestoration->model_type;
        $item = $modelClass::withTrashed()->find($dataRestoration->model_id);

        return view('data-restorations.show', compact('dataRestoration', 'item'));
    }

    // Admins approve or reject
    public function update(Request $request, DataRestorationRequest $dataRestoration): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($dataRestoration->status !== 'pending') {
            return redirect()->route('data-restorations.index')->with('error', 'This request has already been processed.');
        }

        $dataRestoration->update([
            'status' => $validated['status'],
            'admin_id' => Auth::id(),
        ]);

        if ($validated['status'] === 'approved') {
            $modelClass = $dataRestoration->model_type;
            $item = $modelClass::onlyTrashed()->find($dataRestoration->model_id);
            if ($item) {
                $item->restore();
            }
        }

        return redirect()->route('data-restorations.index')->with('status', 'Restoration request ' . $validated['status'] . '.');
    }
}
