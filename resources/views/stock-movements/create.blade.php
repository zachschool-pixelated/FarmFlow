<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Record Stock Movement') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Add a stock-in or stock-out entry for a product.') }}</p>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="mx-auto max-w-2xl">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60" x-data="{ movementType: '{{ old('type', 'in') }}' }">
                <form method="POST" action="{{ route('stock-movements.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="product_id" :value="__('Product')" />
                        <select id="product_id" name="product_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500" required>
                            <option value="">{{ __('Select Product') }}</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                    {{ $product->name }} — {{ __('Stock') }}: {{ $product->stock_quantity }} {{ $product->unit }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label :value="__('Movement Type')" />
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition-all duration-200"
                                   :class="movementType === 'in' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 ring-1 ring-emerald-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                                   @click="movementType = 'in'">
                                <input type="radio" name="type" value="in" class="sr-only" x-model="movementType">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 19 12 5"/><polyline points="5 12 12 5 19 12"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Stock In') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Add inventory') }}</p>
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition-all duration-200"
                                   :class="movementType === 'out' ? 'border-red-500 bg-red-50 dark:bg-red-900/40 ring-1 ring-red-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                                   @click="movementType = 'out'">
                                <input type="radio" name="type" value="out" class="sr-only" x-model="movementType">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-400">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 5 12 19"/><polyline points="19 12 12 19 5 12"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Stock Out') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Remove inventory') }}</p>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity')" />
                            <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full rounded-xl" :value="old('quantity')" min="1" required />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="reference" :value="__('Reference (optional)')" />
                            <x-text-input id="reference" name="reference" class="mt-1 block w-full rounded-xl" :value="old('reference')" placeholder="PO#, Invoice#, etc." />
                            <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes (optional)')" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500" placeholder="{{ __('Reason for this stock movement...') }}">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <x-primary-button>{{ __('Record Movement') }}</x-primary-button>
                        <a href="{{ route('stock-movements.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
