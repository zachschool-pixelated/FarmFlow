<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Stock Movement Details') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('View the full details of this inventory movement.') }}</p>
            </div>
            <a href="{{ route('stock-movements.index') }}" class="btn-secondary">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="mx-auto max-w-2xl space-y-5">
            <!-- Movement Summary Card -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="flex items-center gap-4 mb-6">
                    @if($movement->type === 'in')
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                            <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 19 12 5"/><polyline points="5 12 12 5 19 12"/></svg>
                        </div>
                    @else
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 text-red-700">
                            <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 5 12 19"/><polyline points="19 12 12 19 5 12"/></svg>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $movement->type === 'in' ? __('Stock In') : __('Stock Out') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">
                            {{ $movement->created_at->format('F d, Y — h:i A') }}
                        </p>
                    </div>
                    <div class="ml-auto">
                        <span class="text-3xl font-bold {{ $movement->type === 'in' ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Product -->
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Product') }}</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $movement->product?->name ?? '—' }}</p>
                    </div>

                    <!-- Performed By -->
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Performed By') }}</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $movement->user?->name ?? '—' }}</p>
                    </div>

                    <!-- Stock Before -->
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Stock Before') }}</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $movement->stock_before }} {{ __('units') }}</p>
                    </div>

                    <!-- Stock After -->
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Stock After') }}</p>
                        <p class="text-sm font-bold {{ $movement->type === 'in' ? 'text-emerald-700' : 'text-red-700' }}">{{ $movement->stock_after }} {{ __('units') }}</p>
                    </div>
                </div>
            </div>

            <!-- Reference & Notes -->
            @if($movement->reference || $movement->notes)
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">{{ __('Additional Information') }}</h4>

                    @if($movement->reference)
                        <div class="mb-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Reference') }}</p>
                            <span class="inline-flex items-center rounded-lg bg-farm-100 px-3 py-1.5 text-sm font-medium text-farm-800">{{ $movement->reference }}</span>
                        </div>
                    @endif

                    @if($movement->notes)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1">{{ __('Notes') }}</p>
                            <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed">{{ $movement->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
