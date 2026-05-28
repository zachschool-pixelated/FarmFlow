<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $supplier->name }} {{ __('Dashboard') }}</h2>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $supplier->is_blacklisted ? 'bg-red-100 text-red-700' : ($supplier->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300') }}">{{ $supplier->status_label }}</span>
                    <span class="inline-flex items-center rounded-full bg-farm-100 px-3 py-1 text-xs font-semibold text-farm-700">{{ $supplier->supplier_code }}</span>
                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Contacts: :count', ['count' => $contactCount]) }}</span>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Company overview, requests, contacts, and product modules.') }}</p>
            </div>
            <a href="{{ route('stock-requests.index') }}" class="page-header-btn">{{ __('Stock Requests') }}</a>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-6">
        @if($supplier->is_blacklisted)
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">{{ __('This supplier is blacklisted.') }}</p>
                <p class="mt-1">{{ $supplier->blacklist_reason ?: __('No reason was provided.') }}</p>
            </div>
        @endif

        <div class="grid gap-5 md:grid-cols-3">
            <a href="{{ route('products.index', ['supplier_id' => $supplier->id]) }}" class="stat-card group relative rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-farm-300 dark:hover:ring-farm-600 hover:shadow-md transition-all duration-300">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Products Linked') }}</p>
                <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ $productCount }}</p>
                <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-farm-400 dark:text-farm-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            </a>
            <a href="{{ route('stock-requests.index') }}" class="stat-card group relative rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-amber-300 dark:hover:ring-amber-600 hover:shadow-md transition-all duration-300">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Pending Requests') }}</p>
                <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingRequests }}</p>
                <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-amber-400 dark:text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            </a>
            <a href="#low-stock-products" class="stat-card group relative rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-red-300 dark:hover:ring-red-600 hover:shadow-md transition-all duration-300">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Low Stock Products') }}</p>
                <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ $lowStockProducts->count() }}</p>
                <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <svg class="h-4 w-4 text-red-400 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            </a>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Supplier Performance') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Quick snapshot of request flow and fulfillment.') }}</p>
                </div>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($performanceStats as $stat)
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 lg:col-span-1">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Modules') }}</h3>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('products.index', ['supplier_id' => $supplier->id]) }}" class="block rounded-xl border border-gray-200 dark:border-gray-600 p-4 hover:border-farm-300 hover:bg-farm-50 dark:hover:bg-gray-700 transition-colors">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Product Catalog') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('View products tied to this company.') }}</p>
                    </a>
                    <a href="{{ route('stock-requests.index') }}" class="block rounded-xl border border-gray-200 dark:border-gray-600 p-4 hover:border-farm-300 hover:bg-farm-50 dark:hover:bg-gray-700 transition-colors">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Stock Requests') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Review incoming or pending requests.') }}</p>
                    </a>
                    <a href="{{ route('supplier-profile-requests.create') }}" class="block rounded-xl border border-gray-200 dark:border-gray-600 p-4 hover:border-farm-300 hover:bg-farm-50 dark:hover:bg-gray-700 transition-colors">
                        <p class="font-semibold text-gray-900 dark:text-white">{{ __('Company Profile') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Update company contact information.') }}</p>
                    </a>
                </div>
            </div>

            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 lg:col-span-2">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Recent Stock Requests') }}</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Request') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Product') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Qty') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse ($recentRequests as $request)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">#REQ-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $request->product->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $request->quantity_requested }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                        @php
                                            $statusClass = match($request->status) {
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'processing' => 'bg-blue-100 text-blue-700',
                                                'shipped' => 'bg-indigo-100 text-indigo-700',
                                                'completed' => 'bg-green-100 text-green-700',
                                                'rejected' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        @if(auth()->user() && auth()->user()->isSupplier())
                                            @if(in_array($request->status, ['pending', 'processing', 'shipped']))
                                                <form method="POST" action="{{ route('stock-requests.update', $request) }}" class="inline-flex gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-xs shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 py-1 pl-2 pr-8" onchange="this.form.submit()">
                                                        <option value="pending" @selected($request->status == 'pending')>Pending</option>
                                                        <option value="processing" @selected($request->status == 'processing')>Processing</option>
                                                        <option value="shipped" @selected($request->status == 'shipped')>Shipped</option>
                                                        <option value="rejected" @selected($request->status == 'rejected')>Reject</option>
                                                    </select>
                                                </form>
                                            @else
                                                <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Locked</span>
                                            @endif
                                        @else
                                            <a href="{{ route('stock-requests.index') }}" class="text-xs font-medium text-farm-600 hover:text-farm-800">View in requests</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No requests yet.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('stock-requests.index') }}" class="text-sm font-semibold text-farm-600 hover:text-farm-700 hover:underline">{{ __('View All Requests →') }}</a>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Contacts') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Primary and additional contacts for this supplier.') }}</p>
                </div>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Role') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Phone') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Email') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Type') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->contact_person ?: __('Not set') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ __('Primary Contact') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $supplier->phone ?: '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $supplier->email ?: '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ __('Main record') }}</td>
                        </tr>
                        @forelse($supplier->contacts as $contact)
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $contact->name ?: '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $contact->role ?: '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $contact->phone ?: '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $contact->email ?: '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    @if($contact->is_primary)
                                        <span class="inline-flex items-center rounded-full bg-farm-100 px-2.5 py-1 text-xs font-semibold text-farm-700">{{ __('Primary Additional Contact') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Additional Contact') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No additional contacts added yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="low-stock-products" class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 scroll-mt-24">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Low Stock Products') }}</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Product') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Stock / Reorder') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($lowStockProducts as $product)
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $product->stock_quantity }}</span>
                                    <span class="text-gray-400 dark:text-gray-500">/</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $product->reorder_level }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No low stock products for this company.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>