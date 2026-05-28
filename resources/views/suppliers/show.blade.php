<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Supplier Profile') }}</h2>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('View complete details, catalog, and transactions for this supplier.') }}</p>
            </div>
            
            <div class="flex items-center gap-2">
                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('suppliers.transaction-history', $supplier) }}" class="btn-secondary">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        {{ __('Transactions') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <style>
        .supplier-trans-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 2px 6px !important;
            font-size: 9px !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }
        .badge-completed {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
        }
        .dark .badge-completed {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
        }
        .badge-shipped {
            background-color: #dbeafe !important;
            color: #1e40af !important;
        }
        .dark .badge-shipped {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #60a5fa !important;
        }
        .badge-processing {
            background-color: #fef3c7 !important;
            color: #92400e !important;
        }
        .dark .badge-processing {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
        }
        .badge-pending {
            background-color: #e5e7eb !important;
            color: #374151 !important;
        }
        .dark .badge-pending {
            background-color: rgba(156, 163, 175, 0.15) !important;
            color: #d1d5db !important;
        }
        
        /* Product catalog badges */
        .badge-instock {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
        }
        .dark .badge-instock {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
        }
        .badge-lowstock {
            background-color: #fef3c7 !important;
            color: #92400e !important;
        }
        .dark .badge-lowstock {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
        }
        .badge-outstock {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
        }
        .dark .badge-outstock {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #f87171 !important;
        }
    </style>

    <div class="animate-fade-in mx-auto max-w-5xl space-y-6" x-data="{ activeTab: 'products' }">
        <!-- Main Info Card -->
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                    <div class="flex items-center sm:items-start gap-6">
                        <!-- Profile Picture -->
                        <div class="flex-shrink-0">
                            @if ($supplier->profile_picture)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($supplier->profile_picture) }}" alt="{{ $supplier->name }}" class="h-20 w-20 rounded-full object-cover shadow-sm ring-4 ring-gray-50 dark:ring-gray-700/50">
                            @else
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-farm-100 dark:bg-farm-900/30 text-2xl font-bold text-farm-600 dark:text-farm-400 shadow-sm ring-4 ring-gray-50 dark:ring-gray-700/50">
                                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <span class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-semibold text-gray-800 dark:text-gray-300">
                                    {{ $supplier->supplier_code }}
                                </span>
                                @if($supplier->is_blacklisted)
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 dark:bg-red-900/30 px-2.5 py-1 text-xs font-semibold text-red-700 dark:text-red-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>BLACKLISTED
                                    </span>
                                @elseif($supplier->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-green-100 dark:bg-green-900/30 px-2.5 py-1 text-xs font-semibold text-green-700 dark:text-green-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>ACTIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>INACTIVE
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $supplier->name }}</h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $supplier->contact_person ? 'Primary Contact: ' . $supplier->contact_person : 'No primary contact specified.' }}</p>
                        </div>
                    </div>
                </div>
                
                @if($supplier->is_blacklisted && $supplier->blacklist_reason)
                    <div class="mt-6 rounded-xl bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-900/50">
                        <h4 class="text-sm font-semibold text-red-800 dark:text-red-400 mb-1">{{ __('Blacklist Reason') }}</h4>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ $supplier->blacklist_reason }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Products Card -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-farm-100 dark:bg-farm-900/40 text-farm-700 dark:text-farm-400">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_products'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">{{ __('Products Offered') }}</p>
                </div>
            </div>

            <!-- Total Requests Card -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-400">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_requests'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">{{ __('Total Orders') }}</p>
                </div>
            </div>

            <!-- Total Units Supplied Card -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_units_supplied']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">{{ __('Units Received') }}</p>
                </div>
            </div>

            <!-- Last Delivery / Sent Stock Card -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex items-center gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 flex-shrink-0">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div class="min-w-0 flex-1">
                    @if($lastDelivery)
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate" title="{{ $lastDelivery->product->name }}">
                            {{ $lastDelivery->quantity_requested }} units of {{ $lastDelivery->product->name }}
                        </p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">
                            {{ __('Last Stock Sent') }}: {{ $lastDelivery->shipped_at ? $lastDelivery->shipped_at->diffForHumans() : ($lastDelivery->updated_at ? $lastDelivery->updated_at->diffForHumans() : '') }}
                        </p>
                    @else
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ __('No deliveries yet') }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">{{ __('Last Stock Sent') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'border-farm-600 text-farm-600 dark:border-farm-400 dark:text-farm-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                <span>{{ __('Products Offered') }}</span>
                <span class="rounded bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 text-xs text-gray-600 dark:text-gray-400">{{ $supplier->products->count() }}</span>
            </button>
            <button @click="activeTab = 'transactions'" :class="activeTab === 'transactions' ? 'border-farm-600 text-farm-600 dark:border-farm-400 dark:text-farm-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>{{ __('Transactions History') }}</span>
                <span class="rounded bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 text-xs text-gray-600 dark:text-gray-400">{{ $stockRequests->count() }}</span>
            </button>
            <button @click="activeTab = 'contacts'" :class="activeTab === 'contacts' ? 'border-farm-600 text-farm-600 dark:border-farm-400 dark:text-farm-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-all flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>{{ __('Contact & Address') }}</span>
            </button>
        </div>

        <!-- Tab 1: Products Offered -->
        <div x-show="activeTab === 'products'" class="space-y-4">
            <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Category') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Stock Level') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Price') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($supplier->products as $prod)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                        <a href="{{ route('products.show', $prod) }}" class="hover:underline text-farm-600 dark:text-farm-400">{{ $prod->name }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <span class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-900 px-2.5 py-1 text-gray-600 dark:text-gray-300 font-semibold">{{ $prod->category?->name ?? __('Uncategorized') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $prod->stock_quantity }} <span class="text-xs text-gray-400">{{ $prod->unit }} / reorder: {{ $prod->reorder_level }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                        ₱{{ number_format($prod->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $isOut = $prod->stock_quantity == 0;
                                            $isLow = $prod->stock_quantity <= $prod->reorder_level && !$isOut;
                                        @endphp
                                        @if($isOut)
                                            <span class="supplier-trans-badge badge-outstock">{{ __('Out of Stock') }}</span>
                                        @elseif($isLow)
                                            <span class="supplier-trans-badge badge-lowstock">{{ __('Low Stock') }}</span>
                                        @else
                                            <span class="supplier-trans-badge badge-instock">{{ __('In Stock') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('No products cataloged under this supplier.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 2: Transaction History -->
        <div x-show="activeTab === 'transactions'" class="space-y-4" x-cloak>
            <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Date & Time') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Product') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Quantity') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Expected Delivery') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Created By') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($stockRequests as $req)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $req->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                        <a href="{{ route('stock-requests.show', $req) }}" class="hover:underline text-farm-600 dark:text-farm-400">{{ $req->product->name }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $req->quantity_requested }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($req->status === 'completed')
                                            <span class="supplier-trans-badge badge-completed">{{ __('Completed') }}</span>
                                        @elseif($req->status === 'shipped')
                                            <span class="supplier-trans-badge badge-shipped">{{ __('Shipped') }}</span>
                                        @elseif($req->status === 'processing')
                                            <span class="supplier-trans-badge badge-processing">{{ __('Processing') }}</span>
                                        @else
                                            <span class="supplier-trans-badge badge-pending">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-300">
                                        {{ $req->expected_delivery_at ? $req->expected_delivery_at->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $req->user->name ?? 'System' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('No transactions logged with this supplier.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 3: Contact & Address -->
        <div x-show="activeTab === 'contacts'" class="grid grid-cols-1 md:grid-cols-3 gap-6" x-cloak>
            <!-- Address and Details Card -->
            <div class="md:col-span-1 space-y-6">
                <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 space-y-6">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('Core Contact Details') }}</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('Phone') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->phone ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('Email') }}</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}" class="text-farm-600 hover:underline dark:text-farm-400">{{ $supplier->email }}</a>
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-700" />

                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">{{ __('Address details') }}</h3>
                        <div class="text-sm text-gray-900 dark:text-white space-y-1">
                            @if($supplier->address)
                                <p class="font-semibold">{{ $supplier->address }}</p>
                            @endif
                            @if($supplier->street_address)
                                <p>{{ $supplier->street_address }}</p>
                            @endif
                            @if($supplier->barangay || $supplier->city || $supplier->province)
                                <p>
                                    {{ implode(', ', array_filter([$supplier->barangay, $supplier->city, $supplier->province])) }}
                                    {{ $supplier->postal_code }}
                                </p>
                            @endif
                            @if(!$supplier->address && !$supplier->street_address && !$supplier->city)
                                <p class="text-gray-500 dark:text-gray-400 italic">{{ __('No address details cataloged.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Contacts Card -->
            <div class="md:col-span-2">
                <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Company Contacts') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($supplier->contacts as $contact)
                            <div class="p-6 sm:flex sm:items-center sm:justify-between gap-6 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-farm-100 dark:bg-farm-900/30 text-sm font-bold text-farm-600 dark:text-farm-400">
                                        {{ strtoupper(substr($contact->name ?: 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $contact->name ?: 'Unnamed Contact' }}</h4>
                                            @if($contact->is_primary)
                                                <span class="rounded-md bg-blue-50 dark:bg-blue-900/30 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800">PRIMARY</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $contact->role ?: 'No Role' }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 flex flex-col sm:items-end text-sm text-gray-500 dark:text-gray-400 space-y-1">
                                    @if($contact->email)
                                        <a href="mailto:{{ $contact->email }}" class="hover:text-farm-600 dark:hover:text-farm-400 flex items-center gap-2">
                                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                            {{ $contact->email }}
                                        </a>
                                    @endif
                                    @if($contact->phone)
                                        <span class="flex items-center gap-2">
                                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            {{ $contact->phone }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('No supplementary contacts cataloged for this supplier.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- System Data Info -->
        <div class="flex items-center justify-between px-2 text-[10px] text-gray-400 dark:text-gray-500">
            <p>{{ __('Added on') }} {{ $supplier->created_at->format('M d, Y h:i A') }}</p>
            <p>{{ __('Last updated') }} {{ $supplier->updated_at->diffForHumans() }}</p>
        </div>
    </div>
</x-app-layout>
