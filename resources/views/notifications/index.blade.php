<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('System Notifications') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('A comprehensive list of active requests, low stock alerts, and profile changes.') }}
            </p>
        </div>
    </x-slot>

    <style>
        .notif-page-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 3px 8px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }
        .notif-badge-delivery-today { background-color: #fef08a !important; color: #854d0e !important; }
        .dark .notif-badge-delivery-today { background-color: rgba(250, 204, 21, 0.15) !important; color: #facc15 !important; }
        .notif-badge-delivery-overdue { background-color: #fee2e2 !important; color: #991b1b !important; }
        .dark .notif-badge-delivery-overdue { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
        .notif-badge-low { background-color: #fef3c7 !important; color: #92400e !important; }
        .dark .notif-badge-low { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
        .notif-badge-out { background-color: #fee2e2 !important; color: #991b1b !important; }
        .dark .notif-badge-out { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
        .notif-badge-edit { background-color: #e0e7ff !important; color: #3730a3 !important; }
        .dark .notif-badge-edit { background-color: rgba(99, 102, 241, 0.15) !important; color: #818cf8 !important; }
        .notif-badge-profile { background-color: #f3e8ff !important; color: #6b21a8 !important; }
        .dark .notif-badge-profile { background-color: rgba(168, 85, 247, 0.15) !important; color: #c084fc !important; }
        .notif-badge-restore { background-color: #ccfbf1 !important; color: #075985 !important; }
        .dark .notif-badge-restore { background-color: rgba(20, 184, 166, 0.15) !important; color: #2dd4bf !important; }
        .notif-badge-new { background-color: #dbeafe !important; color: #1e40af !important; }
        .dark .notif-badge-new { background-color: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
    </style>

    <div class="animate-fade-in space-y-6">
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($notifications as $notif)
                    <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all duration-150">
                        <div class="space-y-1 min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">
                                    {{ $notif['title'] }}
                                </h3>
                                <span class="notif-page-badge {{ $notif['badge_class'] }}">
                                    {{ $notif['badge_text'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $notif['message'] }}
                            </p>
                            @if(isset($notif['time']))
                                <p class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($notif['time'])->diffForHumans() }} ({{ \Carbon\Carbon::parse($notif['time'])->format('M d, Y h:i A') }})
                                </p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ $notif['link'] }}" class="inline-flex items-center gap-1.5 rounded-xl bg-farm-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-farm-700 focus:outline-none focus:ring-2 focus:ring-farm-500 focus:ring-offset-2 transition-all">
                                <span>{{ __('View Details') }}</span>
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-16 text-center">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 text-gray-400 dark:text-gray-500 mb-3">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        </div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('No notifications') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('All caught up! You have no active notifications requiring attention.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
