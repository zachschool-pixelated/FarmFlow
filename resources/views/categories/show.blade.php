<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Category Details') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('View category information and associated products.') }}</p>
            </div>
            <a href="{{ route('categories.index') }}" class="btn-secondary">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="mx-auto max-w-4xl space-y-5">
            <!-- Category Summary Card -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- Name -->
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Name') }}</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $category->name }}</p>
                    </div>

                    <!-- Products Count -->
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Total Products') }}</p>
                        <p class="text-sm font-bold text-farm-700">{{ $category->products->count() }} {{ __('products') }}</p>
                    </div>

                    <!-- Supplier -->
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ __('Supplier') }}</p>
                            @if(!$category->supplier_id && auth()->user()->isAdmin())
                                <button type="button" x-data="" @click="$dispatch('open-modal', 'add-supplier')" class="text-xs font-medium text-farm-600 hover:text-farm-800 transition-colors hover:underline">
                                    + Add Supplier
                                </button>
                            @endif
                        </div>
                        @if($category->supplier_id)
                            <a href="{{ route('suppliers.show', $category->supplier) }}" class="text-sm font-bold text-farm-700 hover:underline">
                                {{ $category->supplier->name }}
                            </a>
                        @else
                            <p class="text-sm text-amber-600 font-medium flex items-center gap-1.5">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                This category has no supplier
                            </p>
                        @endif
                    </div>
                </div>

                @if($category->description)
                <div class="mt-4 rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Description') }}</p>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $category->description }}</p>
                </div>
                @endif
            </div>

            <!-- Associated Products -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Products in this Category') }}</h3>
                
                @if($category->products->count() > 0)
                <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Name') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Unit') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Price') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Stock') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @foreach($category->products as $product)
                                <tr class="table-row-hover">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $product->unit }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">₱{{ number_format($product->price, 2) }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $isLow = $product->stock_quantity <= $product->reorder_level;
                                            $isOut = $product->stock_quantity == 0;
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-900 dark:text-white') }}">
                                                {{ $product->stock_quantity }}
                                            </span>
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-8 rounded-xl bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-200 dark:border-gray-600">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                        <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No products found in this category.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin() && !$category->supplier_id)
    <x-modal name="add-supplier" :show="$errors->has('supplier_id')" focusable>
        <form method="post" action="{{ route('categories.update', $category) }}" class="p-6">
            @csrf
            @method('patch')

            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ __('Assign Supplier to Category') }}
            </h2>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">
                {{ __('Select a supplier to manage and provide products for this category.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="supplier_id" :value="__('Supplier')" />
                <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 bg-white dark:bg-gray-800" required>
                    <option value="" disabled selected>{{ __('Select a supplier...') }}</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->supplier_code }})</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" x-on:click="$dispatch('close')" class="btn-secondary">
                    {{ __('Cancel') }}
                </button>

                <x-primary-button>
                    {{ __('Save Supplier') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    @endif
</x-app-layout>
