<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Supplier Profile Edit Requests') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Review and approve changes to supplier company profiles.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-6">
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Request') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Supplier') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Reviewed By') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($requests as $request)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">#PREQ-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($request->supplier->profile_picture)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($request->supplier->profile_picture) }}" alt="" class="h-8 w-8 rounded-full object-cover">
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-farm-100 dark:bg-farm-900/30 text-xs font-bold text-farm-600 dark:text-farm-400">
                                                {{ strtoupper(substr($request->supplier->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col">
                                            <a href="{{ route('suppliers.show', $request->supplier) }}" class="text-sm font-semibold text-farm-600 hover:text-farm-700 dark:text-farm-400 dark:hover:text-farm-300">
                                                {{ $request->supplier->name }}
                                            </a>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $request->supplier->supplier_code }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = match($request->status) {
                                            'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'rejected' => 'bg-red-100 text-red-700 border-red-200',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 border-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $request->created_at->format('M d, Y') }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $request->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($request->reviewer)
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->reviewer->name }}</span>
                                    @else
                                        <span class="text-sm italic text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('supplier-profile-requests.show', $request) }}" class="action-link-edit">
                                        @if($request->status === 'pending')
                                            {{ __('Review') }}
                                        @else
                                            {{ __('View Details') }}
                                        @endif
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No profile edit requests found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="border-t border-gray-100 dark:border-gray-700 p-4">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
