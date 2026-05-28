<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Request Stock for Company') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Create a request for the selected product and company.') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn-secondary">{{ __('Back to Products') }}</a>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="mx-auto max-w-2xl space-y-5">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Product') }}</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $product->category?->name ?? __('Uncategorized') }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Company / Supplier') }}</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->supplier?->name ?? __('No company assigned') }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $product->supplier?->contact_person ?: __('No contact person listed') }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60" x-data="{ qty: {{ old('quantity_requested', max(10, $product->reorder_level * 2)) }} }">
                <form id="stock-request-form" method="POST" action="{{ route('stock-requests.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div>
                        <x-input-label for="quantity_requested" :value="__('Quantity to Request')" />
                        <x-text-input id="quantity_requested" name="quantity_requested" type="number" min="1" class="mt-1 block w-full rounded-xl" x-model="qty" placeholder="Example: 20" required />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Enter how many units you want this company to send.</p>
                        <x-input-error :messages="$errors->get('quantity_requested')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notes">
                            {{ __('Notes') }} <span class="text-xs text-gray-400 dark:text-gray-500 font-normal ml-1">({{ __('Optional') }})</span>
                        </x-input-label>
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500" placeholder="Example: Requesting restock due to low inventory.">{{ old('notes') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Optional details for the company handling the request.</p>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <x-primary-button type="button" x-on:click.prevent="$dispatch('open-modal', 'confirm-stock-request')">{{ __('Send Stock Request') }}</x-primary-button>
                        <a href="{{ route('products.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>

                <x-modal name="confirm-stock-request" focusable>
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Confirm Stock Request') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Are you sure you want to send this stock request? Please confirm the details below:
                        </p>
                        <div class="mt-4 rounded-xl bg-gray-50 dark:bg-gray-900 p-4 space-y-2 border border-gray-100 dark:border-gray-700">
                            <p class="text-sm"><span class="font-semibold text-gray-500 dark:text-gray-400">Product:</span> <span class="text-gray-900 dark:text-gray-100">{{ $product->name }}</span></p>
                            <p class="text-sm"><span class="font-semibold text-gray-500 dark:text-gray-400">Supplier:</span> <span class="text-gray-900 dark:text-gray-100">{{ $product->supplier?->name ?? 'None' }}</span></p>
                            <p class="text-sm"><span class="font-semibold text-gray-500 dark:text-gray-400">Quantity:</span> <strong class="text-farm-600 dark:text-farm-400" x-text="qty"></strong> <span class="text-gray-900 dark:text-gray-100">units</span></p>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button x-on:click="document.getElementById('stock-request-form').submit()">
                                {{ __('Confirm & Send') }}
                            </x-primary-button>
                        </div>
                    </div>
                </x-modal>
            </div>
        </div>
    </div>
</x-app-layout>