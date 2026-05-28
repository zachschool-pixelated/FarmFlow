<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Products') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Track farm supply inventory and stock levels.') }}</p>
            </div>
            <a href="{{ route('products.create') }}" class="page-header-btn">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ __('New Product') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-5" x-data="{ viewMode: localStorage.getItem('productViewMode') || 'list' }" x-init="$watch('viewMode', val => localStorage.setItem('productViewMode', val))">
        @if($selectedFilter === 'low_stock')
            <div class="rounded-2xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/20 p-4 shadow-sm">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200">{{ __('Showing Low Stock Products Only') }}</h4>
                            <p class="text-xs text-amber-700 dark:text-amber-400">{{ __('These items have stock levels at or below their reorder point.') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white dark:bg-gray-800 px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 shadow-sm ring-1 ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        {{ __('Clear Filter') }}
                    </a>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-full sm:w-auto sm:min-w-[200px]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-1.5">{{ __('Category') }}</label>
                    <select name="category_id" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategoryId == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center flex-wrap gap-2 sm:ml-auto">
                    <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    <a href="{{ route('products.index') }}" class="btn-secondary">{{ __('Reset') }}</a>
                    
                    <div class="hidden sm:flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 p-1 ml-2">
                        <button type="button" @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white dark:bg-gray-700 text-farm-600 dark:text-farm-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'" class="rounded-md p-1.5 transition-all">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        </button>
                        <button type="button" @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white dark:bg-gray-700 text-farm-600 dark:text-farm-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'" class="rounded-md p-1.5 transition-all">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products View Containers -->
        <div>
            <!-- List View -->
            <div x-show="viewMode === 'list'" class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Category') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Supplier') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Unit') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Price') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Cost Price') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Stock') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($products as $product)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-lg bg-farm-100 px-2.5 py-1 text-xs font-medium text-farm-800">
                                        {{ $product->category?->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $product->supplier?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $product->unit }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">₱{{ number_format($product->cost_price, 2) }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $isLow = $product->stock_quantity <= $product->reorder_level;
                                        $isOut = $product->stock_quantity == 0;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-900 dark:text-white') }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">/</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $product->reorder_level }}</span>
                                        @if($isOut)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-700">
                                                <span class="h-1 w-1 rounded-full bg-red-500"></span>OUT
                                            </span>
                                        @elseif($isLow)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                                <span class="h-1 w-1 rounded-full bg-amber-500"></span>LOW
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div x-data="{ dropdownOpen: false }" class="relative flex justify-end">
                                        <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-700 dark:text-gray-200 transition-colors">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                            </svg>
                                        </button>

                                        <div x-show="dropdownOpen" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-4 top-10 z-50 mt-1 w-48 rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                             x-cloak>
                                            <div class="py-1 text-left text-sm">
                                                <a href="{{ route('products.show', $product) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900">
                                                    <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                    {{ __('View') }}
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                                                    <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    {{ __('Edit Product') }}
                                                </a>
                                                <a href="{{ route('stock-requests.create', $product) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                                                    <svg class="mr-3 h-4 w-4 text-farm-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                                    {{ __('Request Stock') }}
                                                </a>
                                                @if($product->stock_quantity == 0 && empty($product->supplier_id))
                                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex w-full items-center px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                            <svg class="mr-3 h-4 w-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                            {{ __('Delete Product') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="flex w-full items-center px-4 py-2 text-gray-400 dark:text-gray-500 cursor-not-allowed bg-gray-50/50 dark:bg-gray-800/50" title="{{ __('Cannot delete: Product has stock or is bound to a supplier.') }}">
                                                        <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                        {{ __('Delete Product') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No products found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($products as $product)
                @php
                    $isLow = $product->stock_quantity <= $product->reorder_level;
                    $isOut = $product->stock_quantity == 0;
                @endphp
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 hover:shadow-md transition-all">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <span class="inline-flex items-center rounded-lg bg-farm-100 dark:bg-farm-900/40 px-2.5 py-1 text-xs font-medium text-farm-800 dark:text-farm-400">
                                {{ $product->category?->name ?? '—' }}
                            </span>
                            
                            <div x-data="{ dropdownOpen: false }" class="relative">
                                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-700 dark:text-gray-200 transition-colors">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" /></svg>
                                </button>
                                <div x-show="dropdownOpen" x-transition class="absolute right-0 top-8 z-50 w-48 rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak>
                                    <div class="py-1 text-left text-sm">
                                        <a href="{{ route('products.show', $product) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900">
                                            <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            {{ __('View') }}
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                                            <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            {{ __('Edit') }}
                                        </a>
                                        <a href="{{ route('stock-requests.create', $product) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                                            <svg class="mr-3 h-4 w-4 text-farm-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                            {{ __('Request Stock') }}
                                        </a>
                                        @if($product->stock_quantity == 0 && empty($product->supplier_id))
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex w-full items-center px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                    <svg class="mr-3 h-4 w-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex w-full items-center px-4 py-2 text-gray-400 dark:text-gray-500 cursor-not-allowed bg-gray-50/50 dark:bg-gray-800/50" title="{{ __('Cannot delete: Product has stock or is bound to a supplier.') }}">
                                                <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                {{ __('Delete') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-white line-clamp-1">{{ $product->name }}</h3>
                        <div class="mt-1 flex items-baseline gap-1">
                            <span class="text-xl font-bold text-gray-900 dark:text-white">₱{{ number_format($product->price, 2) }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">/ {{ $product->unit }}</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Cost:') }} ₱{{ number_format($product->cost_price, 2) }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Supplier:') }} <span class="font-medium text-gray-700 dark:text-gray-300">{{ $product->supplier?->name ?? '—' }}</span></p>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">{{ __('Stock Level') }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-900 dark:text-white') }}">
                                    {{ $product->stock_quantity }}
                                </span>
                                <span class="text-sm text-gray-400 dark:text-gray-500">/ {{ $product->reorder_level }}</span>
                            </div>
                        </div>
                        @if($isOut)
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-1 text-xs font-semibold text-red-700 dark:text-red-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>OUT
                            </span>
                        @elseif($isLow)
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-900/30 px-2 py-1 text-xs font-semibold text-amber-700 dark:text-amber-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>LOW
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl bg-white dark:bg-gray-800 p-16 text-center shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                        <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No products found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
        {{ $products->links() }}
    </div>
        </div>
    </div>
</x-app-layout>