<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center rounded-full bg-white dark:bg-gray-800 p-2 text-gray-500 dark:text-gray-400 dark:text-gray-500 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-700 dark:text-gray-200">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                </a>
                <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">
                    {{ __('Report Preview: ') }} {{ ucfirst($type) }}
                </h2>
            </div>
            <a href="{{ route('reports.export', ['type' => $type]) }}" class="btn-primary">
                {{ __('Download PDF') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12 animate-fade-in">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="p-6 overflow-x-auto">
                    @if($type === 'inventory')
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-3">Product Name</th>
                                    <th class="px-4 py-3">Category</th>
                                    <th class="px-4 py-3 text-right">Stock Qty</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($data as $product)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                                        <td class="px-4 py-3">{{ $product->category->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-right">{{ $product->stock_quantity }}</td>
                                        <td class="px-4 py-3">
                                            @if($product->stock_quantity <= 0)
                                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Out of Stock</span>
                                            @elseif($product->stock_quantity <= $product->reorder_level)
                                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Low Stock</span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">In Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    @elseif($type === 'movements')
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Product</th>
                                    <th class="px-4 py-3 text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($data as $movement)
                                    <tr>
                                        <td class="px-4 py-3">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $movement->type)) }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $movement->product->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-right">{{ $movement->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    @elseif($type === 'suppliers')
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-3">Supplier Name</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Primary Contact</th>
                                    <th class="px-4 py-3">Email</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($data as $supplier)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $supplier->name }}</td>
                                        <td class="px-4 py-3">
                                            @if($supplier->is_blacklisted)
                                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Blacklisted</span>
                                            @elseif($supplier->is_active)
                                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-900 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $supplier->contact_person ?: 'N/A' }}</td>
                                        <td class="px-4 py-3">{{ $supplier->email ?: 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    @elseif($type === 'ledger')
                        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $data['product']->name }}</h3>
                                <p class="text-sm text-gray-500">{{ __('Ledger for') }} <span class="font-semibold">{{ $data['month'] }}</span></p>
                            </div>
                        </div>
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Date') }}</th>
                                    <th class="px-4 py-3 text-right">{{ __('Starting Stock') }}</th>
                                    <th class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400">{{ __('Units Added (In)') }}</th>
                                    <th class="px-4 py-3 text-right text-red-600 dark:text-red-400">{{ __('Units Removed (Out)') }}</th>
                                    <th class="px-4 py-3 text-right font-bold">{{ __('Ending Stock') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($data['ledger'] as $day)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                        <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($day['date'])->format('M d, Y (l)') }}</td>
                                        <td class="px-4 py-3 text-right text-gray-500">{{ number_format($day['start_stock']) }}</td>
                                        <td class="px-4 py-3 text-right text-emerald-600 dark:text-emerald-400 font-medium">
                                            {{ $day['in'] > 0 ? '+' . number_format($day['in']) : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-right text-red-600 dark:text-red-400 font-medium">
                                            {{ $day['out'] > 0 ? '-' . number_format($day['out']) : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">{{ number_format($day['end_stock']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('No ledger records found for this month.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
