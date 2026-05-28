<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\StockRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('suppliers.index', [
            'suppliers' => Supplier::query()->withCount('contacts')->orderByDesc('id')->paginate(10),
        ]);
    }

    public function blacklisted(): View
    {
        return view('suppliers.blacklisted', [
            'suppliers' => Supplier::query()->where('is_blacklisted', true)->withCount('contacts')->orderByDesc('id')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('suppliers.create', [
            'supplier' => new Supplier(['is_active' => true]),
            'contactRows' => [
                ['name' => '', 'role' => '', 'phone' => '', 'email' => '', 'notes' => '', 'is_primary' => false],
            ],
        ]);
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $supplier = DB::transaction(function () use ($validated, $request) {
            $payload = $this->supplierPayload($validated);
            
            if ($request->hasFile('profile_picture')) {
                $payload['profile_picture'] = $request->file('profile_picture')->store('suppliers', 'public');
            }

            $supplier = Supplier::create($payload);
            $this->syncContacts($supplier, $validated['contacts'] ?? []);

            if (!empty($validated['create_account'])) {
                \App\Models\User::create([
                    'name' => $validated['account_name'],
                    'email' => $validated['account_email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['account_password']),
                    'plain_password' => $validated['account_password'],
                    'role' => 'supplier',
                    'supplier_id' => $supplier->id,
                ]);
            }

            return $supplier;
        });

        return redirect()->route('suppliers.index')->with('status', $supplier->supplier_code . ' created successfully.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['contacts', 'products.category']);

        $stockRequests = StockRequest::with(['product.category', 'user'])
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->get();

        $lastDelivery = StockRequest::with('product')
            ->where('supplier_id', $supplier->id)
            ->whereIn('status', ['completed', 'shipped'])
            ->latest('shipped_at')
            ->latest('updated_at')
            ->first();

        $stats = [
            'total_products' => $supplier->products->count(),
            'total_requests' => $stockRequests->count(),
            'completed_requests' => $stockRequests->where('status', 'completed')->count(),
            'active_requests' => $stockRequests->whereIn('status', ['pending', 'processing', 'shipped'])->count(),
            'total_units_supplied' => $stockRequests->where('status', 'completed')->sum('quantity_requested'),
        ];

        return view('suppliers.show', compact('supplier', 'stockRequests', 'lastDelivery', 'stats'));
    }

    public function transactionHistory(Supplier $supplier): View
    {
        $user = Auth::user();
        if ($user->role !== 'manager') {
            abort(403);
        }

        $stockRequests = StockRequest::with(['product.category', 'user'])
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->paginate(15, ['*'], 'requests_page');

        $stockMovements = \App\Models\StockMovement::with(['product.category', 'user'])
            ->whereHas('product', fn ($q) => $q->where('supplier_id', $supplier->id))
            ->latest()
            ->paginate(15, ['*'], 'movements_page');

        $stats = [
            'total_requests'    => StockRequest::where('supplier_id', $supplier->id)->count(),
            'completed'         => StockRequest::where('supplier_id', $supplier->id)->where('status', 'completed')->count(),
            'pending'           => StockRequest::where('supplier_id', $supplier->id)->where('status', 'pending')->count(),
            'total_units_in'    => StockRequest::where('supplier_id', $supplier->id)->where('status', 'completed')->sum('quantity_requested'),
        ];

        return view('suppliers.transaction_history', compact('supplier', 'stockRequests', 'stockMovements', 'stats'));
    }

    public function dashboard(Supplier $supplier): View
    {
        $user = Auth::user();

        if (in_array($user->role, ['manager', 'admin'])) {
            abort(403, 'Only suppliers can access the supplier dashboard.');
        }

        if ($user?->isSupplier() && $user->supplier_id !== $supplier->id) {
            abort(403);
        }

        $supplier->load('contacts');

        $products = $supplier->products()->with('category')->get();
        $requests = StockRequest::with(['product', 'user'])
            ->where('supplier_id', $supplier->id)
            ->get();

        $totalRequests = $requests->count();
        $completedRequests = $requests->where('status', 'completed')->count();
        $pendingRequestCount = $requests->where('status', 'pending')->count();
        $fulfillmentRate = $totalRequests > 0 ? (int) round(($completedRequests / $totalRequests) * 100) : 0;

        return view('suppliers.dashboard', [
            'supplier' => $supplier,
            'productCount' => $products->count(),
            'contactCount' => $supplier->contacts->count(),
            'lowStockProducts' => $products
                ->filter(fn ($product) => $product->stock_quantity <= $product->reorder_level)
                ->sortBy('stock_quantity')
                ->take(5)
                ->values(),
            'pendingRequests' => $pendingRequestCount,
            'recentRequests' => $requests->sortByDesc('created_at')->take(5)->values(),
            'performanceStats' => [
                ['label' => 'Total Requests', 'value' => $totalRequests],
                ['label' => 'Completed Requests', 'value' => $completedRequests],
                ['label' => 'Fulfillment Rate', 'value' => $fulfillmentRate . '%'],
                ['label' => 'Average Request Qty', 'value' => $totalRequests > 0 ? round($requests->avg('quantity_requested'), 1) : 0],
            ],
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        $supplier->load('contacts');

        return view('suppliers.edit', [
            'supplier' => $supplier,
            'contactRows' => $supplier->contacts->map(function (SupplierContact $contact): array {
                return [
                    'name' => $contact->name,
                    'role' => $contact->role,
                    'phone' => $contact->phone,
                    'email' => $contact->email,
                    'notes' => $contact->notes,
                    'is_primary' => $contact->is_primary,
                ];
            })->values()->all() ?: [
                ['name' => '', 'role' => '', 'phone' => '', 'email' => '', 'notes' => '', 'is_primary' => false],
            ],
        ]);
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($supplier, $validated, $request): void {
            $payload = $this->supplierPayload($validated);

            if ($request->hasFile('profile_picture')) {
                if ($supplier->profile_picture) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($supplier->profile_picture);
                }
                $payload['profile_picture'] = $request->file('profile_picture')->store('suppliers', 'public');
            }

            $supplier->update($payload);
            $this->syncContacts($supplier, $validated['contacts'] ?? []);
        });

        return redirect()->route('suppliers.index')->with('status', 'Supplier updated successfully.');
    }


    public function toggleBlacklist(\Illuminate\Http\Request $request, Supplier $supplier): RedirectResponse
    {
        $isBlacklisting = !$supplier->is_blacklisted;
        
        $validated = $request->validate([
            'blacklist_reason' => $isBlacklisting ? ['required', 'string', 'max:1000'] : ['nullable'],
        ]);

        $supplier->update([
            'is_blacklisted' => $isBlacklisting,
            'is_active' => $isBlacklisting ? false : $supplier->is_active,
            'blacklist_reason' => $isBlacklisting ? $validated['blacklist_reason'] : null,
        ]);

        $message = $isBlacklisting ? 'Supplier has been blacklisted.' : 'Supplier has been removed from the blacklist.';

        return redirect()->route('suppliers.index')->with('status', $message);
    }

    private function supplierPayload(array $validated): array
    {
        $isActive = isset($validated['is_active']) ? ! empty($validated['is_active']) : true;
        $isBlacklisted = ! empty($validated['is_blacklisted']);

        $fullAddress = trim(implode(', ', array_filter([
            $validated['street_address'] ?? null,
            $validated['barangay'] ?? null,
            $validated['city'] ?? null,
            $validated['province'] ?? null,
            $validated['postal_code'] ?? null
        ])));

        return [
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $fullAddress ?: ($validated['address'] ?? null),
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'barangay' => $validated['barangay'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'street_address' => $validated['street_address'] ?? null,
            'is_active' => $isBlacklisted ? false : $isActive,
            'is_blacklisted' => $isBlacklisted,
            'blacklist_reason' => $isBlacklisted ? ($validated['blacklist_reason'] ?? null) : null,
        ];
    }

    private function syncContacts(Supplier $supplier, array $contacts): void
    {
        $supplier->contacts()->delete();

        collect($contacts)
            ->filter(function (array $contact): bool {
                return filled($contact['name'] ?? null)
                    || filled($contact['role'] ?? null)
                    || filled($contact['phone'] ?? null)
                    || filled($contact['email'] ?? null)
                    || filled($contact['notes'] ?? null)
                    || ! empty($contact['is_primary']);
            })
            ->each(function (array $contact) use ($supplier): void {
                $supplier->contacts()->create([
                    'name' => $contact['name'] ?? null,
                    'role' => $contact['role'] ?? null,
                    'phone' => $contact['phone'] ?? null,
                    'email' => $contact['email'] ?? null,
                    'notes' => $contact['notes'] ?? null,
                    'is_primary' => ! empty($contact['is_primary']),
                ]);
            });
    }
}