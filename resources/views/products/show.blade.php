<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Product Details') }}</h2>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('View full information about this farm supply.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('stock-requests.create', $product) }}" class="inline-flex items-center gap-2 rounded-xl bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-farm-600 dark:text-farm-400 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    {{ __('Request Stock') }}
                </a>
                <a href="{{ route('products.edit', $product) }}" class="page-header-btn">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    {{ __('Edit Product') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in mx-auto max-w-5xl space-y-6">
        <!-- Main Info Card -->
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="inline-flex items-center rounded-lg bg-farm-100 dark:bg-farm-900/40 px-2.5 py-1 text-xs font-medium text-farm-800 dark:text-farm-400">
                                {{ $product->category?->name ?? 'Uncategorized' }}
                            </span>
                            @php
                                $isLow = $product->stock_quantity <= $product->reorder_level;
                                $isOut = $product->stock_quantity == 0;
                            @endphp
                            @if($isOut)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 dark:bg-red-900/30 px-2.5 py-1 text-xs font-semibold text-red-700 dark:text-red-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>OUT OF STOCK
                                </span>
                            @elseif($isLow)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-100 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:text-amber-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>LOW STOCK
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-green-100 dark:bg-green-900/30 px-2.5 py-1 text-xs font-semibold text-green-700 dark:text-green-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>IN STOCK
                                </span>
                            @endif
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h1>
                        <p class="mt-2 text-base text-gray-500 dark:text-gray-400 max-w-2xl">{{ $product->description ?: 'No description provided.' }}</p>
                    </div>
                    
                    <div class="flex-shrink-0 text-left sm:text-right">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Selling Price') }}</p>
                        <div class="mt-1 flex items-baseline sm:justify-end gap-1">
                            <span class="text-4xl font-extrabold text-gray-900 dark:text-white">₱{{ number_format($product->price, 2) }}</span>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">/ {{ $product->unit }}</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Cost Price:') }} ₱{{ number_format($product->cost_price, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 dark:divide-gray-700 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="p-6 sm:p-8">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">{{ __('Stock Information') }}</h3>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Current Quantity') }}</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->stock_quantity }} {{ $product->unit }}s</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Reorder Level') }}</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->reorder_level }} {{ $product->unit }}s</dd>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Inventory Value') }}</dt>
                            <dd class="text-sm font-bold text-farm-600 dark:text-farm-400">₱{{ number_format($product->stock_quantity * $product->cost_price, 2) }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div class="p-6 sm:p-8">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">{{ __('Supplier Information') }}</h3>
                    @if($product->supplier)
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Company Name') }}</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $product->supplier->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Contact') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->supplier->contact_person ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Email') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($product->supplier->email)
                                        <a href="mailto:{{ $product->supplier->email }}" class="text-farm-600 hover:text-farm-700 dark:text-farm-400 dark:hover:text-farm-300">{{ $product->supplier->email }}</a>
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-6">
                            <a href="{{ route('suppliers.show', $product->supplier) }}" class="text-sm font-semibold text-farm-600 hover:text-farm-700 dark:text-farm-400 dark:hover:text-farm-300 flex items-center gap-1">
                                {{ __('View Full Supplier Profile') }}
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            </a>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <svg class="h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5c-1.1 0-2 .9-2 2v2"/><circle cx="8.5" cy="7" r="4"/><line x1="18" y1="8" x2="23" y2="13"/><line x1="23" y1="8" x2="18" y2="13"/></svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No supplier is currently assigned to this product.') }}</p>
                            <a href="{{ route('products.edit', $product) }}" class="mt-3 text-sm font-semibold text-farm-600 hover:text-farm-700 dark:text-farm-400 dark:hover:text-farm-300">
                                {{ __('Assign a Supplier') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- System Data -->
        <div class="flex items-center justify-between px-2 text-xs text-gray-400 dark:text-gray-500">
            <p>{{ __('Added on') }} {{ $product->created_at->format('M d, Y h:i A') }}</p>
            <p>{{ __('Last updated') }} {{ $product->updated_at->diffForHumans() }}</p>
        </div>
    </div>
</x-app-layout>
