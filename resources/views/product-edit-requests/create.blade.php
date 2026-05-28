<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Request Product Edit') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Submit a change request for manager review.') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn-secondary">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in mx-auto max-w-3xl space-y-6">

        @if($existingPending)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-950/20 p-5">
            <p class="font-semibold text-amber-800 dark:text-amber-400">⏳ You already have a pending edit request for this product.</p>
            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">Please wait for a manager to review your existing request before submitting a new one.</p>
        </div>
        @else

        {{-- Current Product Info --}}
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">{{ __('Current Product Information') }}</h3>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Name</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Price</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">₱{{ number_format($product->price, 2) }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Unit</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $product->unit }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Category</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $product->category?->name ?? '—' }}</p>
                </div>
                @if($product->description)
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-3 col-span-2 sm:col-span-4">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Description</p>
                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">{{ $product->description }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Edit Request Form --}}
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ __('Requested Changes') }}</h3>
            <p class="mb-5 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Only fill in the fields you want to change. Leave blank to keep current values.') }}</p>

            <form method="POST" action="{{ route('supplier-requests.store', $product) }}" class="space-y-5">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="new_name" :value="__('New Name')" />
                        <x-text-input id="new_name" name="new_name" class="mt-1 block w-full rounded-xl" :value="old('new_name')" placeholder="{{ $product->name }}" />
                        <x-input-error :messages="$errors->get('new_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="new_unit" :value="__('New Unit')" />
                        <x-text-input id="new_unit" name="new_unit" class="mt-1 block w-full rounded-xl" :value="old('new_unit')" placeholder="{{ $product->unit }}" />
                        <x-input-error :messages="$errors->get('new_unit')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="new_price" :value="__('New Price (₱)')" />
                        <x-text-input id="new_price" name="new_price" type="number" step="0.01" class="mt-1 block w-full rounded-xl" :value="old('new_price')" placeholder="{{ $product->price }}" />
                        <x-input-error :messages="$errors->get('new_price')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="new_description" :value="__('New Description')" />
                        <textarea id="new_description" name="new_description" rows="3"
                            class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 text-sm"
                            placeholder="{{ $product->description ?? 'Enter a new description...' }}">{{ old('new_description') }}</textarea>
                        <x-input-error :messages="$errors->get('new_description')" class="mt-2" />
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-5">
                    <x-input-label for="reason" :value="__('Reason for Edit *')" />
                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Explain why these changes are needed. This will be reviewed by a manager.') }}</p>
                    <textarea id="reason" name="reason" rows="4" required
                        class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 text-sm"
                        placeholder="e.g. Price was updated per our new contract effective June 2026...">{{ old('reason') }}</textarea>
                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>{{ __('Submit Edit Request') }}</x-primary-button>
                    <a href="{{ route('products.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
