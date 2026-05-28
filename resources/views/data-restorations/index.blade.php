<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Data Restoration Requests') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Review requests to undelete data.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-8">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Pending Requests') }}</h3>
            <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Requester') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Model / ID') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Reason') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($pendingRequests as $req)
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $req->user->name ?? 'Unknown' }}<br>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $req->created_at->format('M d, Y H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @php $modelBase = class_basename($req->model_type); @endphp
                                        <span class="font-semibold text-gray-900 dark:text-gray-200">{{ $modelBase }}</span> #{{ $req->model_id }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate" title="{{ $req->reason }}">
                                        {{ $req->reason }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900/30 px-2 py-1 text-xs font-semibold text-yellow-800 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-900/50">{{ __('Pending') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('data-restorations.show', $req) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold text-farm-600 hover:bg-farm-50 hover:text-farm-800 transition-colors">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            {{ __('Review') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No pending requests.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($pendingRequests->hasPages())
                    <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                        {{ $pendingRequests->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Request History') }}</h3>
            <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Requester') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Model / ID') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($historyRequests as $req)
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $req->user->name ?? 'Unknown' }}<br>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $req->created_at->format('M d, Y H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @php $modelBase = class_basename($req->model_type); @endphp
                                        <span class="font-semibold text-gray-900 dark:text-gray-200">{{ $modelBase }}</span> #{{ $req->model_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($req->status === 'approved')
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50">{{ __('Approved') }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/30 px-2 py-1 text-xs font-semibold text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/50">{{ __('Rejected') }}</span>
                                        @endif
                                        <br>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">by {{ $req->admin->name ?? 'System' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('data-restorations.show', $req) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 hover:text-gray-800 transition-colors">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            {{ __('View Details') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No request history.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($historyRequests->hasPages())
                    <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                        {{ $historyRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
