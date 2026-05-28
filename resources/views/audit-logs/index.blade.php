<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('System Audit Logs') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Track system changes and user activities.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Timestamp') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('User') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Action') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Module') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Changes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($logs as $log)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $log->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $log->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-farm-100 text-[10px] font-bold text-farm-700">
                                            {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->user?->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $actionClass = match($log->action) {
                                            'created' => 'bg-green-100 text-green-700',
                                            'updated' => 'bg-blue-100 text-blue-700',
                                            'deleted' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $actionClass }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ class_basename($log->auditable_type) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">ID: {{ $log->auditable_id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-md overflow-hidden text-sm">
                                        @if($log->action === 'updated')
                                            <div class="space-y-1">
                                                @foreach(($log->new_values ?? []) as $key => $newValue)
                                                    @if($key !== 'updated_at')
                                                        <div class="flex items-center gap-2 text-xs">
                                                            <span class="font-medium text-gray-600 dark:text-gray-300">{{ $key }}:</span>
                                                            <span class="text-red-500 line-through">{{ Str::limit(json_encode($log->old_values[$key] ?? ''), 20) }}</span>
                                                            <svg class="h-3 w-3 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                                            <span class="text-green-600 font-medium">{{ Str::limit(json_encode($newValue), 20) }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            @if($log->auditable)
                                                <form action="{{ route('audit-logs.revert', $log) }}" method="POST" class="mt-3" onsubmit="return confirm('Are you sure you want to revert these changes?');">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-md bg-white dark:bg-gray-700 px-2.5 py-1.5 text-xs font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        <svg class="h-3.5 w-3.5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>
                                                        {{ __('Revert Edit') }}
                                                    </button>
                                                </form>
                                            @endif
                                        @elseif($log->action === 'created')
                                            <span class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ count($log->new_values ?? []) }} fields set.</span>
                                        @elseif($log->action === 'deleted')
                                            <span class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ count($log->old_values ?? []) }} fields removed.</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No audit logs found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
