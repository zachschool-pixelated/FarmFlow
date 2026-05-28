<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Stock Movements') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Track all inventory stock-in and stock-out activity.') }}</p>
            </div>
            <a href="{{ route('stock-movements.create') }}" class="page-header-btn">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ __('Record Movement') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-5">
        <!-- Filters -->
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <form method="GET" action="{{ route('stock-movements.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="w-full sm:w-auto sm:min-w-[200px]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-1.5">{{ __('Product') }}</label>
                    <select name="product_id" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500">
                        <option value="">{{ __('All Products') }}</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected($selectedProductId == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto sm:min-w-[160px]">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-1.5">{{ __('Type') }}</label>
                    <select name="type" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white text-sm shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="in" @selected($selectedType === 'in')>{{ __('Stock In') }}</option>
                        <option value="out" @selected($selectedType === 'out')>{{ __('Stock Out') }}</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    <a href="{{ route('stock-movements.index') }}" class="btn-secondary">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>

        <!-- Movements Table -->
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Type') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Quantity') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Stock Change') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Reference') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('By') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($movements as $movement)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $movement->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $movement->created_at->format('h:i A') }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $movement->product?->name ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($movement->voided_at)
                                        <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-900/30 px-2 py-1 text-xs font-bold text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-900/50" title="Reason: {{ $movement->void_reason }}">VOIDED</span>
                                    @elseif($movement->type === 'in')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 19 12 5"/><polyline points="5 12 12 5 19 12"/></svg>
                                            IN
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">
                                            <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="12 5 12 19"/><polyline points="19 12 12 19 5 12"/></svg>
                                            OUT
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold {{ $movement->type === 'in' ? 'text-emerald-700' : 'text-red-700' }}">
                                        {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5 text-sm">
                                        <span class="text-gray-400 dark:text-gray-500">{{ $movement->stock_before }}</span>
                                        <svg class="h-3.5 w-3.5 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $movement->stock_after }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($movement->reference)
                                        <span class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-gray-200">{{ $movement->reference }}</span>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $movement->user?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('stock-movements.show', $movement) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold text-farm-600 hover:bg-farm-50 hover:text-farm-800 transition-colors">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            {{ __('View') }}
                                        </a>
                                        @if(in_array(auth()->user()->role, ['admin', 'manager']) && !$movement->voided_at)
                                            <button type="button" onclick="openVoidModal({{ $movement->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold text-red-600 hover:bg-red-50 hover:text-red-800 transition-colors">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                                {{ __('Void') }}
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('No stock movements yet') }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Record your first stock-in or stock-out to get started.') }}</p>
                                        <a href="{{ route('stock-movements.create') }}" class="mt-4 page-header-btn text-xs">
                                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                            {{ __('Record Movement') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>

    <!-- Void Modal -->
    <div id="voidModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center px-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" aria-hidden="true" onclick="closeVoidModal()"></div>

            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div>
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="modal-title">{{ __('Void Stock Movement') }}</h3>
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <p>{{ __('This will generate a compensating transaction to reverse the inventory change. Please provide a reason.') }}</p>
                        </div>
                    </div>
                </div>
                <form id="voidForm" method="POST" class="mt-5 sm:mt-6">
                    @csrf
                    <div class="mb-4">
                        <label for="void_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Void Reason') }}</label>
                        <input type="text" name="void_reason" id="void_reason" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-farm-500 focus:ring-farm-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white sm:text-sm" required placeholder="Mistyped quantity, etc.">
                    </div>
                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:text-sm">
                            {{ __('Confirm Void') }}
                        </button>
                        <button type="button" class="w-full sm:w-auto mt-3 sm:mt-0 btn-secondary" onclick="closeVoidModal()">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openVoidModal(id) {
            document.getElementById('voidForm').action = `/stock-movements/${id}/void`;
            document.getElementById('voidModal').classList.remove('hidden');
        }

        function closeVoidModal() {
            document.getElementById('voidModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
