<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    @if (request()->query('status') === 'completed')
                        {{ __('Restock History') }}
                    @else
                        {{ auth()->user()->isSupplier() ? __('Incoming Stock Requests') : __('Stock Requests') }}
                    @endif
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">
                    @if (request()->query('status') === 'completed')
                        {{ __('View your history of completed restocks.') }}
                    @else
                        {{ auth()->user()->isSupplier() ? __('Manage requests from FarmFlow.') : __('Manage requests sent to your suppliers.') }}
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-5">

        @if(auth()->user()->isSupplier())
        {{-- Status Filter Tabs --}}
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $tabs = [
                        'all'        => ['label' => 'All',        'color' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200'],
                        'pending'    => ['label' => 'Pending',    'color' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200'],
                        'processing' => ['label' => 'Processing', 'color' => 'bg-blue-100 text-blue-700 hover:bg-blue-200'],
                        'shipped'    => ['label' => 'Shipped',    'color' => 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'],
                        'completed'  => ['label' => 'Completed',  'color' => 'bg-green-100 text-green-700 hover:bg-green-200'],
                        'rejected'   => ['label' => 'Rejected',   'color' => 'bg-red-100 text-red-700 hover:bg-red-200'],
                    ];
                    $active = $statusFilter ?? 'all';
                @endphp
                @foreach($tabs as $key => $tab)
                    @php
                        $count = $statusCounts->get($key === 'all' ? null : $key);
                        $isActive = $active === $key || ($key === 'all' && !$statusFilter);
                    @endphp
                    <a href="{{ route('stock-requests.index', $key !== 'all' ? ['status' => $key] : []) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors
                              {{ $isActive ? 'ring-2 ring-offset-1 ring-farm-500 ' . $tab['color'] : $tab['color'] }}">
                        {{ $tab['label'] }}
                        @if($key !== 'all' && $statusCounts->get($key))
                            <span class="rounded-full bg-white dark:bg-gray-800/60 px-1.5 py-0.5 text-[10px] font-bold">
                                {{ $statusCounts->get($key) }}
                            </span>
                        @elseif($key === 'all')
                            <span class="rounded-full bg-white dark:bg-gray-800/60 px-1.5 py-0.5 text-[10px] font-bold">
                                {{ $statusCounts->sum() }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Request ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Product') }}</th>
                            @if(!auth()->user()->isSupplier())
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Supplier') }}</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Quantity') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Date / Delivery') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($requests as $request)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    #REQ-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $request->product->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $request->product->category->name ?? 'Uncategorized' }}</div>
                                </td>
                                @if(!auth()->user()->isSupplier())
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->supplier->name }}</div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $request->quantity_requested }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $request->product->unit }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = match($request->status) {
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'shipped' => 'bg-indigo-100 text-indigo-700',
                                            'completed' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                                    @if($request->status === 'shipped' && $request->shipped_at)
                                        <p class="mt-1 text-xs font-semibold text-indigo-600">
                                            Shipped {{ $request->shipped_at->format('M d, Y') }}
                                        </p>
                                    @elseif($request->status === 'completed' && $request->shipped_at)
                                        <p class="mt-1 text-xs font-semibold text-green-600">
                                            Delivered {{ $request->expected_delivery_at ? $request->expected_delivery_at->format('M d, Y') : $request->updated_at->format('M d, Y') }}
                                        </p>
                                    @endif
                                    @if($request->expected_delivery_at && $request->status !== 'completed')
                                        @php
                                            $isToday = $request->expected_delivery_at->isToday();
                                            $isPast = $request->expected_delivery_at->isPast() && !$isToday;
                                        @endphp
                                        
                                        <div class="mt-1.5 flex flex-col gap-1">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">ETA:</span> <span class="text-farm-600 dark:text-farm-400 font-semibold">{{ $request->expected_delivery_at->format('M d, Y') }}</span>
                                            </p>
                                            
                                            @if(!auth()->user()->isSupplier())
                                                @if($isPast)
                                                    <span class="inline-flex w-fit items-center gap-1 rounded-md bg-red-50 dark:bg-red-900/30 px-2 py-0.5 text-[10px] font-bold text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/20 dark:ring-red-500/20">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> OVERDUE
                                                    </span>
                                                @elseif($isToday)
                                                    <span class="inline-flex w-fit items-center gap-1 rounded-md bg-yellow-50 dark:bg-yellow-900/30 px-2 py-0.5 text-[10px] font-bold text-yellow-700 dark:text-yellow-400 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-500/20">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-yellow-500 animate-pulse"></span> ARRIVING TODAY
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    @if(auth()->user()->isSupplier())
                                        @if(in_array($request->status, ['pending', 'processing', 'shipped']))
                                            <a href="{{ route('stock-requests.show', $request) }}" class="inline-flex items-center gap-1 rounded-lg bg-farm-50 dark:bg-farm-900/30 px-3 py-1.5 text-xs font-semibold text-farm-700 dark:text-farm-400 hover:bg-farm-100 dark:hover:bg-farm-900/50 transition-colors">
                                                Review Request
                                            </a>
                                        @else
                                            <a href="{{ route('stock-requests.show', $request) }}" class="inline-flex items-center gap-1 rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                View Details
                                            </a>
                                        @endif
                                    @else
                                        @if($request->status === 'shipped')
                                            <form id="restock-form-{{ $request->id }}" method="POST" action="{{ route('stock-requests.update', $request) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="button" x-on:click.prevent="$dispatch('open-modal', 'confirm-restock-{{ $request->id }}')" class="inline-flex items-center gap-1 rounded-lg bg-green-50 px-2 py-1 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors">
                                                    Mark Completed (Restock)
                                                </button>
                                            </form>

                                            <x-modal name="confirm-restock-{{ $request->id }}" focusable>
                                                <div class="p-6 whitespace-normal text-left">
                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                        {{ __('Confirm Stock Receipt') }}
                                                    </h2>
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                        Are you sure you have received this stock? This action will officially add <strong class="text-farm-600 dark:text-farm-400">{{ $request->quantity_requested }} units</strong> of {{ $request->product->name }} to your inventory.
                                                    </p>
                                                    <div class="mt-6 flex justify-end gap-3">
                                                        <x-secondary-button x-on:click="$dispatch('close')">
                                                            {{ __('Cancel') }}
                                                        </x-secondary-button>
                                                        <x-primary-button x-on:click="document.getElementById('restock-form-{{ $request->id }}').submit()">
                                                            {{ __('Confirm & Restock') }}
                                                        </x-primary-button>
                                                    </div>
                                                </div>
                                            </x-modal>
                                        @elseif($request->status === 'completed')
                                            <span class="text-xs font-medium text-green-600">Restocked</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isSupplier() ? 6 : 7 }}" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No stock requests found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
            <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                {{ $requests->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
