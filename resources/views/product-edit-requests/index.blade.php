<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Supplier Requests') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Review product edit requests submitted by suppliers.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-5">

        {{-- Status Filter Tabs --}}
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $tabs = [
                        'all'      => ['label' => 'All',      'color' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600'],
                        'pending'  => ['label' => 'Pending',  'color' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-950/40 dark:text-yellow-400 dark:hover:bg-yellow-900/50'],
                        'approved' => ['label' => 'Approved', 'color' => 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-950/40 dark:text-green-400 dark:hover:bg-green-900/50'],
                        'rejected' => ['label' => 'Rejected', 'color' => 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-950/40 dark:text-red-400 dark:hover:bg-red-900/50'],
                    ];
                    $active = $status ?? 'all';
                @endphp
                @foreach($tabs as $key => $tab)
                    @php $isActive = $active === $key || ($key === 'all' && !$status); @endphp
                    <a href="{{ route('supplier-requests.index', $key !== 'all' ? ['status' => $key] : []) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors
                              {{ $isActive ? 'ring-2 ring-offset-1 ring-farm-500 ' . $tab['color'] : $tab['color'] }}">
                        {{ $tab['label'] }}
                        @if($key === 'all')
                            <span class="rounded-full bg-white dark:bg-gray-800/60 px-1.5 py-0.5 text-[10px] font-bold">{{ $statusCounts->sum() }}</span>
                        @elseif($statusCounts->get($key))
                            <span class="rounded-full bg-white dark:bg-gray-800/60 px-1.5 py-0.5 text-[10px] font-bold">{{ $statusCounts->get($key) }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Requests Table --}}
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Supplier') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Submitted By') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Changes Requested') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($editRequests as $req)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $req->product?->name ?? '—' }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $req->product?->category?->name ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $req->supplier?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">{{ $req->user?->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_keys($req->requested_changes ?? []) as $field)
                                            <span class="inline-flex items-center rounded-lg bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 text-[10px] font-semibold uppercase text-blue-700 dark:text-blue-400">{{ $field }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $cls = match($req->status) {
                                            'pending'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/40 dark:text-yellow-400',
                                            'approved' => 'bg-green-100 text-green-700 dark:bg-green-950/40 dark:text-green-400',
                                            'rejected' => 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400',
                                            default    => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $cls }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $req->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('supplier-requests.show', $req) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold {{ $req->isPending() ? 'bg-farm-50 text-farm-700 hover:bg-farm-100 dark:bg-farm-950/40 dark:text-farm-400 dark:hover:bg-farm-900/50' : 'text-gray-500 dark:text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900' }} transition-colors">
                                        {{ $req->isPending() ? __('Review') : __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No supplier requests found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($editRequests->hasPages())
            <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                {{ $editRequests->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
