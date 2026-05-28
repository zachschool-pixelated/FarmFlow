<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">
            {{ __('Reports Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 animate-fade-in">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                
                <!-- Inventory Report -->
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Current Inventory') }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ __('A comprehensive list of all active products, their categories, and current stock levels across all locations.') }}</p>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('reports.preview', ['type' => 'inventory']) }}" class="btn-secondary flex-1 text-center">{{ __('Preview') }}</a>
                        <a href="{{ route('reports.export', ['type' => 'inventory']) }}" class="btn-primary flex-1 text-center">{{ __('Export PDF') }}</a>
                    </div>
                </div>

                <!-- Stock Movements Report -->
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Stock Movements') }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ __('A historical ledger of the 500 most recent stock-in, stock-out, and adjustment actions.') }}</p>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('reports.preview', ['type' => 'movements']) }}" class="btn-secondary flex-1 text-center">{{ __('Preview') }}</a>
                        <a href="{{ route('reports.export', ['type' => 'movements']) }}" class="btn-primary flex-1 text-center">{{ __('Export PDF') }}</a>
                    </div>
                </div>

                <!-- Supplier Directory Report -->
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Supplier Directory') }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ __('A complete list of all registered suppliers along with their active status and primary contact information.') }}</p>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('reports.preview', ['type' => 'suppliers']) }}" class="btn-secondary flex-1 text-center">{{ __('Preview') }}</a>
                        <a href="{{ route('reports.export', ['type' => 'suppliers']) }}" class="btn-primary flex-1 text-center">{{ __('Export PDF') }}</a>
                    </div>
                </div>

                <!-- Daily Stock Ledger Report -->
                <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex flex-col justify-between md:col-span-2 lg:col-span-3 mt-4">
                    <form action="{{ route('reports.preview') }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-4" target="_blank">
                        <input type="hidden" name="type" value="ledger">
                        
                        <div class="flex-1">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Daily Stock Ledger') }}</h3>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Track the daily starting balance, incoming, outgoing, and ending stock for a specific product over a selected month.') }}</p>
                            </div>
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <x-input-label for="product_id" :value="__('Select Product')" />
                            <select id="product_id" name="product_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-farm-500 focus:ring-farm-500 text-sm" required>
                                <option value="">{{ __('Choose a product...') }}</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full md:w-48">
                            <x-input-label for="month" :value="__('Select Month')" />
                            <input type="month" id="month" name="month" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-farm-500 focus:ring-farm-500 text-sm" value="{{ now()->format('Y-m') }}" max="{{ now()->format('Y-m') }}" required>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-secondary h-10 px-4">{{ __('Preview') }}</button>
                            <button type="submit" formaction="{{ route('reports.export') }}" class="btn-primary h-10 px-4 whitespace-nowrap">{{ __('Export PDF') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
