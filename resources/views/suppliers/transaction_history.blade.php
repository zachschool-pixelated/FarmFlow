<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('suppliers.show', $supplier) }}" class="inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Transaction History:') }} {{ $supplier->name }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('View past stock requests and activities for this supplier.') }}</p>
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
            background-color: #fef3c7 !important;
            color: #92400e !important;
        }
        .dark .badge-pending {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
        }
        .badge-rejected {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
        }
        .dark .badge-rejected {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #f87171 !important;
        }
        .badge-generic {
            background-color: #f3f4f6 !important;
            color: #374151 !important;
        }
        .dark .badge-generic {
            background-color: rgba(156, 163, 175, 0.15) !important;
            color: #d1d5db !important;
        }
    </style>

    <div class="animate-fade-in space-y-6">
        <!-- Stats -->
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Requests') }}</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_requests'] }}</div>
            </div>
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Completed') }}</div>
                <div class="mt-2 text-3xl font-bold text-emerald-600">{{ $stats['completed'] }}</div>
            </div>
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pending') }}</div>
                <div class="mt-2 text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
            </div>
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Units In') }}</div>
                <div class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($stats['total_units_in']) }}</div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Stock Requests') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Request ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Requested Qty') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($stockRequests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ $request->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200 font-medium">#REQ-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $request->product->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ number_format($request->quantity_requested) }} units</td>
                                <td class="px-6 py-4">
                                    <span class="supplier-trans-badge 
                                        @if($request->status === 'completed') badge-completed 
                                        @elseif($request->status === 'pending') badge-pending 
                                        @elseif($request->status === 'processing') badge-processing 
                                        @elseif($request->status === 'shipped') badge-shipped 
                                        @elseif($request->status === 'rejected') badge-rejected 
                                        @else badge-generic @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('stock-requests.show', $request) }}" class="text-sm font-medium text-farm-600 hover:text-farm-700 dark:text-farm-400 dark:hover:text-farm-300">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">{{ __('No stock requests found for this supplier.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stockRequests->hasPages())
                <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-3">
                    {{ $stockRequests->appends(['movements_page' => request('movements_page')])->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
