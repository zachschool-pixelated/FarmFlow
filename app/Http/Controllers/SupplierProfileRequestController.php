<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\SupplierProfileEditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupplierProfileRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'manager') {
            abort(403);
        }

        $requests = SupplierProfileEditRequest::with(['supplier', 'reviewer'])
            ->latest()
            ->paginate(15);

        return view('suppliers.requests.index', compact('requests'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->isSupplier() || !$user->supplier_id) {
            abort(403, 'Only suppliers can request profile edits.');
        }

        $supplier = Supplier::with('contacts')->findOrFail($user->supplier_id);

        $pendingRequest = SupplierProfileEditRequest::where('supplier_id', $supplier->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return redirect()->route('suppliers.dashboard', $supplier)->with('status', 'You already have a pending profile edit request.');
        }

        $contactRows = $supplier->contacts->map(function ($contact) {
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
        ];

        return view('suppliers.requests.create', compact('supplier', 'contactRows'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isSupplier() || !$user->supplier_id) {
            abort(403);
        }

        $supplier = Supplier::with('contacts')->findOrFail($user->supplier_id);

        $pendingRequest = SupplierProfileEditRequest::where('supplier_id', $supplier->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return redirect()->route('suppliers.dashboard', $supplier)->with('error', 'You already have a pending profile edit request.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'province' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'street_address' => ['nullable', 'string'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['nullable', 'string', 'max:255'],
            'contacts.*.role' => ['nullable', 'string', 'max:255'],
            'contacts.*.phone' => ['nullable', 'string', 'max:50'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.notes' => ['nullable', 'string', 'max:500'],
            'contacts.*.is_primary' => ['nullable', 'boolean'],
        ]);

        $fullAddress = trim(implode(', ', array_filter([
            $validated['street_address'] ?? null,
            $validated['barangay'] ?? null,
            $validated['city'] ?? null,
            $validated['province'] ?? null,
            $validated['postal_code'] ?? null
        ])));

        $requestedChanges = [
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $fullAddress ?: null,
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'barangay' => $validated['barangay'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'street_address' => $validated['street_address'] ?? null,
            'contacts' => $validated['contacts'] ?? [],
        ];

        if ($request->hasFile('profile_picture')) {
            $requestedChanges['profile_picture'] = $request->file('profile_picture')->store('suppliers_temp', 'public');
        }

        $originalData = collect($supplier->toArray())->only([
            'name', 'contact_person', 'phone', 'email', 'address', 'province', 'city', 'barangay', 'postal_code', 'street_address', 'profile_picture'
        ])->toArray();
        $originalData['contacts'] = $supplier->contacts->map->only(['name', 'role', 'phone', 'email', 'notes', 'is_primary'])->toArray();

        SupplierProfileEditRequest::create([
            'supplier_id' => $supplier->id,
            'status' => 'pending',
            'requested_changes' => $requestedChanges,
            'original_data' => $originalData,
        ]);

        return redirect()->route('suppliers.dashboard', $supplier)->with('status', 'Profile edit request submitted successfully and is pending approval.');
    }

    public function show(SupplierProfileEditRequest $supplierProfileRequest)
    {
        $user = Auth::user();
        if ($user->role !== 'manager') {
            abort(403);
        }

        $supplierProfileRequest->load(['supplier', 'reviewer']);
        
        return view('suppliers.requests.show', compact('supplierProfileRequest'));
    }

    public function update(Request $request, SupplierProfileEditRequest $supplierProfileRequest)
    {
        $user = Auth::user();
        if ($user->role !== 'manager') {
            abort(403);
        }

        if ($supplierProfileRequest->status !== 'pending') {
            return redirect()->route('supplier-profile-requests.index')->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'required_if:status,rejected'],
        ]);

        DB::transaction(function () use ($supplierProfileRequest, $validated, $user) {
            if ($validated['status'] === 'approved') {
                $supplier = $supplierProfileRequest->supplier;
                $changes = $supplierProfileRequest->requested_changes;
                
                if (!empty($changes['profile_picture'])) {
                    if ($supplier->profile_picture) {
                        Storage::disk('public')->delete($supplier->profile_picture);
                    }
                    $newPath = str_replace('suppliers_temp/', 'suppliers/', $changes['profile_picture']);
                    if (Storage::disk('public')->exists($changes['profile_picture'])) {
                        Storage::disk('public')->move($changes['profile_picture'], $newPath);
                        $changes['profile_picture'] = $newPath;
                    }
                }

                $supplierData = collect($changes)->except('contacts')->toArray();
                $supplier->update($supplierData);

                if (isset($changes['contacts'])) {
                    $supplier->contacts()->delete();
                    foreach ($changes['contacts'] as $contact) {
                        if (!empty($contact['name']) || !empty($contact['role']) || !empty($contact['email']) || !empty($contact['phone'])) {
                            $supplier->contacts()->create([
                                'name' => $contact['name'] ?? null,
                                'role' => $contact['role'] ?? null,
                                'phone' => $contact['phone'] ?? null,
                                'email' => $contact['email'] ?? null,
                                'notes' => $contact['notes'] ?? null,
                                'is_primary' => !empty($contact['is_primary']),
                            ]);
                        }
                    }
                }
            }

            $supplierProfileRequest->update([
                'status' => $validated['status'],
                'rejection_reason' => $validated['rejection_reason'] ?? null,
                'reviewed_by_id' => $user->id,
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('supplier-profile-requests.index')->with('status', 'Supplier profile edit request ' . $validated['status'] . '.');
    }
}
