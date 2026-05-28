<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('Dashboard') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Welcome back! Here\'s your farm supply overview.') }}
            </p>
        </div>
    </x-slot>

    <style>
        .dashboard-status-badge {
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
        .dashboard-status-badge-in-stock {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
        }
        .dark .dashboard-status-badge-in-stock {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
        }
        .dashboard-status-badge-low-stock {
            background-color: #fef3c7 !important;
            color: #92400e !important;
        }
        .dark .dashboard-status-badge-low-stock {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
        }
        .dashboard-status-badge-out-of-stock {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
        }
        .dark .dashboard-status-badge-out-of-stock {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #f87171 !important;
        }
        .dashboard-status-badge-in {
            background-color: #dbeafe !important;
            color: #1e40af !important;
        }
        .dark .dashboard-status-badge-in {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #60a5fa !important;
        }
        .dashboard-status-badge-out {
            background-color: #ffe4e6 !important;
            color: #9f1239 !important;
        }
        .dark .dashboard-status-badge-out {
            background-color: rgba(244, 63, 94, 0.15) !important;
            color: #fb7185 !important;
        }
    </style>

    <div class="animate-fade-in" id="dashboard-admin-container" x-data="{
        activeModal: null,
        searchQuery: '',
        statusFilter: 'all',
        categoryFilter: 'all',
        openModal(type, filterVal = null) {
            this.activeModal = type;
            this.searchQuery = '';
            if (type === 'breakdown') {
                this.statusFilter = filterVal || 'all';
            } else if (type === 'category') {
                this.categoryFilter = filterVal || 'all';
            }
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.activeModal = null;
            document.body.classList.remove('overflow-hidden');
        }
    }" @open-dashboard-modal.window="openModal($event.detail.type, $event.detail.filter)" @keydown.escape.window="closeModal()">
        @if (auth()->user()?->isAdmin())
            <div class="space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @if (auth()->user()->role === 'admin')
                        <!-- Manager Accounts Card -->
                        <a href="{{ route('users.index') }}" class="stat-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-farm-300 dark:hover:ring-farm-600 hover:shadow-md transition-all duration-300">
                            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-farm-100 dark:bg-farm-900/40 opacity-60 transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-farm-700 dark:text-farm-400">{{ __('Manager Accounts') }}</span>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-farm-100 dark:bg-farm-900/40 text-farm-700 dark:text-farm-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalManagers }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Total manager users') }}</p>
                            </div>
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-4 w-4 text-farm-400 dark:text-farm-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </a>

                        <!-- Restricted Accounts Card -->
                        <a href="{{ route('users.index') }}" class="stat-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-red-300 dark:hover:ring-red-600 hover:shadow-md transition-all duration-300">
                            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-red-100 dark:bg-red-900/40 opacity-60 transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-400">{{ __('Restricted Accounts') }}</span>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $restrictedManagers }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Restricted manager accounts') }}</p>
                            </div>
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-4 w-4 text-red-400 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </a>

                        <!-- Pending Restorations Card -->
                        <a href="{{ route('data-restorations.index') }}" class="stat-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-amber-300 dark:hover:ring-amber-600 hover:shadow-md transition-all duration-300">
                            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-amber-100 dark:bg-amber-900/40 opacity-60 transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">{{ __('Pending Restorations') }}</span>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingRestorations }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Awaiting admin approval') }}</p>
                            </div>
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-4 w-4 text-amber-400 dark:text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </a>

                        <!-- Total Audit Logs Card -->
                        <a href="{{ route('audit-logs.index') }}" class="stat-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-sky-300 dark:hover:ring-sky-600 hover:shadow-md transition-all duration-300">
                            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-sky-100 dark:bg-sky-950 opacity-60 transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-sky-700 dark:text-sky-400">{{ __('System Audit Logs') }}</span>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalAuditLogs }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Recorded activities') }}</p>
                            </div>
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-4 w-4 text-sky-400 dark:text-sky-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </a>
                    @else
                        <!-- Categories Card -->
                        <a href="{{ route('categories.index') }}" class="stat-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-farm-300 dark:hover:ring-farm-600 hover:shadow-md transition-all duration-300">
                            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-farm-100 dark:bg-farm-900/40 opacity-60 transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-farm-700 dark:text-farm-400">{{ __('Categories') }}</span>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-farm-100 dark:bg-farm-900/40 text-farm-700 dark:text-farm-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCategories }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Active categories') }}</p>
                            </div>
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-4 w-4 text-farm-400 dark:text-farm-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </a>

                        <!-- Products Card -->
                        <a href="{{ route('products.index') }}" class="stat-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-emerald-300 dark:hover:ring-emerald-600 hover:shadow-md transition-all duration-300">
                            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-emerald-100 dark:bg-emerald-900/40 opacity-60 transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">{{ __('Products') }}</span>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalProducts }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Total inventory items') }}</p>
                            </div>
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-4 w-4 text-emerald-400 dark:text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </a>

                        <!-- Low Stock Card -->
                        <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" class="stat-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-red-300 dark:hover:ring-red-600 hover:shadow-md transition-all duration-300">
                            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-red-100 dark:bg-red-900/40 opacity-60 transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-400">{{ __('Attention Needed') }}</span>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $lowStockCount }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Below reorder level') }}</p>
                            </div>
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-4 w-4 text-red-400 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </a>

                        <!-- Suppliers Card -->
                        <a href="{{ route('suppliers.index') }}" class="stat-card group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 block hover:ring-sky-300 dark:hover:ring-sky-600 hover:shadow-md transition-all duration-300">
                            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-sky-100 dark:bg-sky-900/40 opacity-60 transition-transform duration-500 group-hover:scale-125"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-sky-700 dark:text-sky-400">{{ __('Suppliers') }}</span>
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                    </div>
                                </div>
                                <p class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $totalSuppliers }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Active partners') }}</p>
                            </div>
                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="h-4 w-4 text-sky-400 dark:text-sky-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </div>
                        </a>
                    @endif
                </div>

                <!-- Charts Section -->
                @if (auth()->user()->role === 'admin')
                    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                        <!-- System Activity (Audit Logs per day) -->
                        <div class="group rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 xl:col-span-2 flex flex-col justify-between transition-all duration-200 hover:ring-farm-300 dark:hover:ring-farm-600 hover:shadow-md">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('System Activity (Audit Logs)') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-6">{{ __('Audit log entries generated over the past 7 days') }}</p>
                            </div>
                            <div class="relative h-72">
                                <canvas id="systemActivityChart"></canvas>
                            </div>
                        </div>

                        <!-- Database Restorations Status Breakdown -->
                        <div class="group rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex flex-col justify-between transition-all duration-200 hover:ring-amber-300 dark:hover:ring-amber-600 hover:shadow-md">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Restorations Status') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-6">{{ __('Database restoration request statuses breakdown') }}</p>
                            </div>
                            <div class="relative flex items-center justify-center h-64">
                                <canvas id="restorationBreakdownChart"></canvas>
                            </div>
                        </div>

                        <!-- Recent Audit Logs Table (Full Width) -->
                        <div class="group rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 xl:col-span-3 flex flex-col justify-between transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Recent System Activities') }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Latest audit logs recorded by the system') }}</p>
                                </div>
                                <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 px-3 py-1.5 text-xs font-bold text-gray-500 dark:text-gray-400 hover:bg-farm-50 dark:hover:bg-farm-900/30 hover:text-farm-600 dark:hover:text-farm-400 transition-colors">
                                    <span>{{ __('View All Logs') }}</span>
                                    <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </div>
                            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Date & Time</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">User</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Action</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Target Type</th>
                                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        @forelse($recentAuditLogs as $log)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $log->created_at->format('M d, Y h:i A') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $log->user->name ?? 'System' }}
                                                </td>
                                                <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300 font-medium">
                                                    <span class="inline-flex rounded-lg px-2 py-0.5 text-[10px] font-bold uppercase
                                                        @if($log->action === 'create' || $log->action === 'restore') bg-green-100 text-green-800 dark:bg-green-950/40 dark:text-green-400
                                                        @elseif($log->action === 'delete') bg-red-100 text-red-800 dark:bg-red-950/40 dark:text-red-400
                                                        @elseif($log->action === 'update' || $log->action === 'revert') bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-400
                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-750 dark:text-gray-300 @endif">
                                                        {{ $log->action }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                                    {{ class_basename($log->auditable_type) }} (ID: {{ $log->auditable_id }})
                                                </td>
                                                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                                    {{ $log->ip_address }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    No system activity recorded yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                        <!-- Stock Status Breakdown -->
                        <div @click="openModal('breakdown')" class="group rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex flex-col justify-between transition-all duration-200 cursor-pointer hover:ring-farm-300 dark:hover:ring-farm-600 hover:shadow-md">
                            <div>
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Stock Breakdown') }}</h3>
                                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 px-2 py-1 text-[10px] font-bold text-gray-500 dark:text-gray-400 group-hover:bg-farm-50 dark:group-hover:bg-farm-900/30 group-hover:text-farm-600 dark:group-hover:text-farm-400 transition-colors">
                                        <span>{{ __('Details') }}</span>
                                        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-6">{{ __('Inventory distribution by stock health (click chart to view)') }}</p>
                            </div>
                            <div class="relative flex items-center justify-center h-64" @click.stop>
                                <canvas id="stockBreakdownChart"></canvas>
                            </div>
                        </div>

                        <!-- Stock Volume by Category -->
                        <div @click="openModal('category')" class="group rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 xl:col-span-2 flex flex-col justify-between transition-all duration-200 cursor-pointer hover:ring-emerald-300 dark:hover:ring-emerald-600 hover:shadow-md">
                            <div>
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Stock by Category') }}</h3>
                                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 px-2 py-1 text-[10px] font-bold text-gray-500 dark:text-gray-400 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                        <span>{{ __('Details') }}</span>
                                        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-6">{{ __('Total product stock count inside each category (click bar to view)') }}</p>
                            </div>
                            <div class="relative h-64" @click.stop>
                                <canvas id="categoryStockChart"></canvas>
                            </div>
                        </div>

                        <!-- Stock Movement Trends (7 Days) -->
                        <div @click="openModal('movements')" class="group rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 xl:col-span-3 flex flex-col justify-between transition-all duration-200 cursor-pointer hover:ring-sky-300 dark:hover:ring-sky-600 hover:shadow-md">
                            <div>
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Stock Movement Trends') }}</h3>
                                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 px-2 py-1 text-[10px] font-bold text-gray-500 dark:text-gray-400 group-hover:bg-sky-50 dark:group-hover:bg-sky-900/30 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">
                                        <span>{{ __('Details') }}</span>
                                        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-6">{{ __('Daily incoming stock (Stock In) vs outgoing stock (Stock Out) over the past 7 days (click chart to view)') }}</p>
                            </div>
                            <div class="relative h-72" @click.stop>
                                <canvas id="movementTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                @endif

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                @if (auth()->user()->role === 'admin')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const getColors = () => {
                                const isDark = document.documentElement.classList.contains('dark');
                                return {
                                    text: isDark ? '#9ca3af' : '#4b5563',
                                    grid: isDark ? '#374151' : '#f3f4f6',
                                    border: isDark ? '#1f2937' : '#ffffff'
                                };
                            };

                            let colors = getColors();

                            // 1. Database Restorations Status (Doughnut)
                            const ctxBreakdown = document.getElementById('restorationBreakdownChart').getContext('2d');
                            const breakdownChart = new Chart(ctxBreakdown, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Pending', 'Approved', 'Rejected'],
                                    datasets: [{
                                        data: @json($restorationBreakdown),
                                        backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                                        borderWidth: 2,
                                        borderColor: colors.border,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '75%',
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 12, weight: '500' },
                                                padding: 20,
                                                usePointStyle: true,
                                                pointStyle: 'circle'
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
                                            titleColor: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827',
                                            bodyColor: colors.text,
                                            borderColor: '#e5e7eb',
                                            borderWidth: document.documentElement.classList.contains('dark') ? 0 : 1,
                                            padding: 10,
                                            boxPadding: 4,
                                            font: { family: "'Inter', sans-serif" }
                                        }
                                    }
                                }
                            });

                            // 2. System Activity (Bar Chart)
                            const ctxActivity = document.getElementById('systemActivityChart').getContext('2d');
                            const activityChart = new Chart(ctxActivity, {
                                type: 'bar',
                                data: {
                                    labels: @json($adminLogLabels),
                                    datasets: [{
                                        label: 'Audit Log Entries',
                                        data: @json($adminLogValues),
                                        backgroundColor: '#2d6a4f',
                                        borderRadius: 6,
                                        maxBarThickness: 32
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            backgroundColor: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
                                            titleColor: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827',
                                            bodyColor: colors.text,
                                            font: { family: "'Inter', sans-serif" }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            ticks: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 11 }
                                            }
                                        },
                                        y: {
                                            grid: { color: colors.grid },
                                            border: { dash: [4, 4] },
                                            ticks: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 11 },
                                                precision: 0
                                            }
                                        }
                                    }
                                }
                            });

                            // Watch for dark mode changes to update chart styling dynamically
                            const observer = new MutationObserver(() => {
                                colors = getColors();
                                const isDark = document.documentElement.classList.contains('dark');
                                const border = isDark ? '#1f2937' : '#ffffff';

                                // Breakdown Chart Update
                                breakdownChart.data.datasets[0].borderColor = border;
                                breakdownChart.options.plugins.legend.labels.color = colors.text;
                                breakdownChart.update();

                                // Activity Chart Update
                                activityChart.options.scales.x.ticks.color = colors.text;
                                activityChart.options.scales.y.ticks.color = colors.text;
                                activityChart.options.scales.y.grid.color = colors.grid;
                                activityChart.update();
                            });
                            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                        });
                    </script>
                @else
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const getColors = () => {
                                const isDark = document.documentElement.classList.contains('dark');
                                return {
                                    text: isDark ? '#9ca3af' : '#4b5563',
                                    grid: isDark ? '#374151' : '#f3f4f6',
                                    border: isDark ? '#1f2937' : '#ffffff'
                                };
                            };

                            let colors = getColors();

                            // 1. Stock Status Breakdown (Doughnut)
                            const ctxBreakdown = document.getElementById('stockBreakdownChart').getContext('2d');
                            const breakdownChart = new Chart(ctxBreakdown, {
                                type: 'doughnut',
                                data: {
                                    labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                                    datasets: [{
                                        data: @json($stockBreakdown),
                                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                                        borderWidth: 2,
                                        borderColor: colors.border,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    cutout: '75%',
                                    onClick: (event, activeElements) => {
                                        if (activeElements.length > 0) {
                                            const index = activeElements[0].index;
                                            const statuses = ['In Stock', 'Low Stock', 'Out of Stock'];
                                            window.dispatchEvent(new CustomEvent('open-dashboard-modal', {
                                                detail: { type: 'breakdown', filter: statuses[index] }
                                            }));
                                        } else {
                                            window.dispatchEvent(new CustomEvent('open-dashboard-modal', {
                                                detail: { type: 'breakdown' }
                                            }));
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 12, weight: '500' },
                                                padding: 20,
                                                usePointStyle: true,
                                                pointStyle: 'circle'
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
                                            titleColor: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827',
                                            bodyColor: colors.text,
                                            borderColor: '#e5e7eb',
                                            borderWidth: document.documentElement.classList.contains('dark') ? 0 : 1,
                                            padding: 10,
                                            boxPadding: 4,
                                            font: { family: "'Inter', sans-serif" }
                                        }
                                    }
                                }
                            });

                            // 2. Stock Volume by Category (Bar Chart)
                            const ctxCategory = document.getElementById('categoryStockChart').getContext('2d');
                            const categoryChart = new Chart(ctxCategory, {
                                type: 'bar',
                                data: {
                                    labels: @json($categoryLabels),
                                    datasets: [{
                                        label: 'Stock Quantity',
                                        data: @json($categoryValues),
                                        backgroundColor: '#059669',
                                        borderRadius: 8,
                                        borderSkipped: false,
                                        maxBarThickness: 32
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    onClick: (event, activeElements) => {
                                        if (activeElements.length > 0) {
                                            const index = activeElements[0].index;
                                            const categoryName = categoryChart.data.labels[index];
                                            window.dispatchEvent(new CustomEvent('open-dashboard-modal', {
                                                detail: { type: 'category', filter: categoryName }
                                            }));
                                        } else {
                                            window.dispatchEvent(new CustomEvent('open-dashboard-modal', {
                                                detail: { type: 'category' }
                                            }));
                                        }
                                    },
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            backgroundColor: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
                                            titleColor: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827',
                                            bodyColor: colors.text,
                                            font: { family: "'Inter', sans-serif" }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            ticks: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 11 }
                                            }
                                        },
                                        y: {
                                            grid: { color: colors.grid },
                                            border: { dash: [4, 4] },
                                            ticks: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 11 },
                                                precision: 0
                                            }
                                        }
                                    }
                                }
                            });

                            // 3. Stock Movement Trends (Line Chart)
                            const ctxMovement = document.getElementById('movementTrendChart').getContext('2d');
                            const movementChart = new Chart(ctxMovement, {
                                type: 'line',
                                data: {
                                    labels: @json($movementLabels),
                                    datasets: [
                                        {
                                            label: 'Stock In',
                                            data: @json($movementIn),
                                            borderColor: '#10b981',
                                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                            fill: true,
                                            tension: 0.4,
                                            borderWidth: 2,
                                            pointRadius: 4,
                                            pointHoverRadius: 6
                                        },
                                        {
                                            label: 'Stock Out',
                                            data: @json($movementOut),
                                            borderColor: '#ef4444',
                                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                            fill: true,
                                            tension: 0.4,
                                            borderWidth: 2,
                                            pointRadius: 4,
                                            pointHoverRadius: 6
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    onClick: () => {
                                        window.dispatchEvent(new CustomEvent('open-dashboard-modal', {
                                            detail: { type: 'movements' }
                                        }));
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                            labels: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 12, weight: '500' },
                                                usePointStyle: true,
                                                pointStyle: 'circle',
                                                padding: 15
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: document.documentElement.classList.contains('dark') ? '#111827' : '#ffffff',
                                            titleColor: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827',
                                            bodyColor: colors.text,
                                            font: { family: "'Inter', sans-serif" }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            ticks: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 11 }
                                            }
                                        },
                                        y: {
                                            grid: { color: colors.grid },
                                            border: { dash: [4, 4] },
                                            ticks: {
                                                color: colors.text,
                                                font: { family: "'Inter', sans-serif", size: 11 },
                                                precision: 0
                                            }
                                        }
                                    }
                                }
                            });

                            // Watch for dark mode changes to update chart styling dynamically
                            const observer = new MutationObserver(() => {
                                colors = getColors();
                                const isDark = document.documentElement.classList.contains('dark');
                                const border = isDark ? '#1f2937' : '#ffffff';

                                // Breakdown Chart Update
                                breakdownChart.data.datasets[0].borderColor = border;
                                breakdownChart.options.plugins.legend.labels.color = colors.text;
                                breakdownChart.update();

                                // Category Chart Update
                                categoryChart.options.scales.x.ticks.color = colors.text;
                                categoryChart.options.scales.y.ticks.color = colors.text;
                                categoryChart.options.scales.y.grid.color = colors.grid;
                                categoryChart.update();

                                // Movement Chart Update
                                movementChart.options.scales.x.ticks.color = colors.text;
                                movementChart.options.scales.y.ticks.color = colors.text;
                                movementChart.options.scales.y.grid.color = colors.grid;
                                movementChart.options.plugins.legend.labels.color = colors.text;
                                movementChart.update();
                            });
                            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
                        });
                    </script>
                @endif

                <!-- Modals Section -->
                @if (auth()->user()->role === 'manager')
                    <div x-show="activeModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <!-- Overlay -->
                        <div x-show="activeModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal()"></div>

                        <!-- Modal Body Container -->
                        <div class="flex min-h-screen items-center justify-center p-4">
                            
                            <!-- Modal 1: Stock Breakdown Details -->
                            <div x-show="activeModal === 'breakdown'" x-transition.scale.origin.center class="relative w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-10 transition-colors duration-200">
                                <!-- Header -->
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Stock Health Details') }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Browse inventory items classified by stock status') }}</p>
                                    </div>
                                    <button type="button" @click="closeModal()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <!-- Search and Filters -->
                                <div class="flex flex-col sm:flex-row gap-3 mb-4">
                                    <!-- Search Input -->
                                    <div class="relative flex-1">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-500">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                        </span>
                                        <input type="text" x-model="searchQuery" placeholder="Search product name..." class="block w-full rounded-xl border-gray-200 pl-9 pr-4 text-sm dark:border-gray-750 dark:bg-gray-900 dark:text-white focus:border-farm-500 focus:ring-farm-500" />
                                    </div>

                                    <!-- Status Tabs -->
                                    <div class="flex rounded-xl bg-gray-100 dark:bg-gray-900 p-1">
                                        <button type="button" @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-955 dark:hover:text-white'" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all">All</button>
                                        <button type="button" @click="statusFilter = 'In Stock'" :class="statusFilter === 'In Stock' ? 'bg-emerald-500 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-500'" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all">In Stock</button>
                                        <button type="button" @click="statusFilter = 'Low Stock'" :class="statusFilter === 'Low Stock' ? 'bg-amber-505 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-amber-500'" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all">Low Stock</button>
                                        <button type="button" @click="statusFilter = 'Out of Stock'" :class="statusFilter === 'Out of Stock' ? 'bg-red-500 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-red-500'" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all">Out of Stock</button>
                                    </div>
                                </div>

                                <!-- List Table -->
                                <div class="overflow-x-auto max-h-[350px] rounded-xl border border-gray-100 dark:border-gray-700">
                                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Product</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Category</th>
                                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Stock / Reorder</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Supplier</th>
                                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                            @foreach($productsDetail as $product)
                                                <tr 
                                                    x-show="(statusFilter === 'all' || 
                                                             (statusFilter === 'In Stock' && {{ $product->stock_quantity }} > {{ $product->reorder_level }}) || 
                                                             (statusFilter === 'Low Stock' && {{ $product->stock_quantity }} <= {{ $product->reorder_level }} && {{ $product->stock_quantity }} > 0) || 
                                                             (statusFilter === 'Out of Stock' && {{ $product->stock_quantity }} == 0)) && 
                                                            (searchQuery === '' || '{{ strtolower($product->name) }}'.includes(searchQuery.toLowerCase()))"
                                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                                                >
                                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</td>
                                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                                                    <td class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $product->stock_quantity }} <span class="text-xs text-gray-400">/ reorder: {{ $product->reorder_level }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $product->supplier?->name ?? 'N/A' }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if($product->stock_quantity == 0)
                                                            <span class="dashboard-status-badge dashboard-status-badge-out-of-stock">Out of Stock</span>
                                                        @elseif($product->stock_quantity <= $product->reorder_level)
                                                            <span class="dashboard-status-badge dashboard-status-badge-low-stock">Low Stock</span>
                                                        @else
                                                            <span class="dashboard-status-badge dashboard-status-badge-in-stock">In Stock</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Modal 2: Stock Volume by Category -->
                            <div x-show="activeModal === 'category'" x-transition.scale.origin.center class="relative w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-10 transition-colors duration-200">
                                <!-- Header -->
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Stock Levels by Category') }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Browse category-specific products and stock counts') }}</p>
                                    </div>
                                    <button type="button" @click="closeModal()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <div class="flex flex-col md:flex-row gap-5">
                                    <!-- Sidebar categories list -->
                                    <div class="w-full md:w-1/4 rounded-xl bg-gray-50 dark:bg-gray-900 p-3 max-h-[350px] overflow-y-auto">
                                        <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-550 uppercase tracking-widest mb-2 px-2">Categories</p>
                                        <div class="space-y-1">
                                            <button type="button" @click="categoryFilter = 'all'" :class="categoryFilter === 'all' ? 'bg-farm-500 text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'" class="w-full text-left rounded-lg px-2.5 py-1.5 text-xs font-semibold flex items-center justify-between transition-all">
                                                <span>All Categories</span>
                                                <span :class="categoryFilter === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-650 dark:text-gray-400'" class="rounded px-1.5 py-0.5 text-[9px] font-bold">{{ $productsDetail->count() }}</span>
                                            </button>
                                            @foreach($categories as $cat)
                                                <button type="button" @click="categoryFilter = '{{ $cat->name }}'" :class="categoryFilter === '{{ $cat->name }}' ? 'bg-farm-500 text-white shadow-sm' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'" class="w-full text-left rounded-lg px-2.5 py-1.5 text-xs font-semibold flex items-center justify-between transition-all">
                                                    <span class="truncate pr-2">{{ $cat->name }}</span>
                                                    <span :class="categoryFilter === '{{ $cat->name }}' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-800 text-gray-650 dark:text-gray-400'" class="rounded px-1.5 py-0.5 text-[9px] font-bold">{{ $cat->products->count() }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Category Products list -->
                                    <div class="w-full md:w-3/4 overflow-x-auto max-h-[350px] rounded-xl border border-gray-100 dark:border-gray-700">
                                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                                                <tr>
                                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Product</th>
                                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Category</th>
                                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Stock / Reorder</th>
                                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Supplier</th>
                                                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                @forelse($productsDetail as $product)
                                                    <tr 
                                                        x-show="categoryFilter === 'all' || categoryFilter === '{{ $product->category?->name ?? 'Uncategorized' }}'"
                                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                                                    >
                                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</td>
                                                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                                                        <td class="px-4 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $product->stock_quantity }} <span class="text-xs text-gray-400">/ reorder: {{ $product->reorder_level }}</span>
                                                        </td>
                                                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $product->supplier?->name ?? 'N/A' }}</td>
                                                        <td class="px-4 py-3 text-center">
                                                            @if($product->stock_quantity == 0)
                                                                <span class="dashboard-status-badge dashboard-status-badge-out-of-stock">Out</span>
                                                            @elseif($product->stock_quantity <= $product->reorder_level)
                                                                <span class="dashboard-status-badge dashboard-status-badge-low-stock">Low</span>
                                                            @else
                                                                <span class="dashboard-status-badge dashboard-status-badge-in-stock">In Stock</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No products inside this category.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal 3: Recent Stock Movements Details -->
                            <div x-show="activeModal === 'movements'" x-transition.scale.origin.center class="relative w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-10 transition-colors duration-200">
                                <!-- Header -->
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Recent Stock Movements') }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Browse daily stock movements from the past 7 days') }}</p>
                                    </div>
                                    <button type="button" @click="closeModal()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>

                                <div class="overflow-x-auto max-h-[350px] rounded-xl border border-gray-100 dark:border-gray-700">
                                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-550 dark:text-gray-400">Date & Time</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-550 dark:text-gray-400">Product</th>
                                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-550 dark:text-gray-400">Type</th>
                                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-550 dark:text-gray-400">Quantity</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-550 dark:text-gray-400">Created By</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-550 dark:text-gray-400">Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                            @forelse($recentMovements as $move)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $move->created_at->format('M d, Y h:i A') }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                                        {{ $move->product->name ?? 'Deleted Product' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if($move->type === 'in')
                                                            <span class="dashboard-status-badge dashboard-status-badge-in">STOCK IN</span>
                                                        @else
                                                            <span class="dashboard-status-badge dashboard-status-badge-out">STOCK OUT</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-white">
                                                        {{ $move->quantity }}
                                                    </td>
                                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">
                                                        {{ $move->user->name ?? 'System' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $move->reason }}
                                                        @if($move->notes)
                                                            <span class="block text-[10px] text-gray-400 italic">{{ $move->notes }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="px-4 py-16 text-center text-sm text-gray-500 dark:text-gray-400">
                                                        {{ __('No stock movements recorded in the last 7 days.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="rounded-2xl border-2 border-amber-200 dark:border-amber-800/50 bg-amber-50 dark:bg-amber-900/20 p-6">
                <div class="flex gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-amber-200 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-amber-900 dark:text-amber-300">{{ __('Access Restricted') }}</h4>
                        <p class="mt-1 text-sm text-amber-800 dark:text-amber-200">{{ __('This dashboard is available to admin accounts only. Please contact your administrator if you believe you should have access.') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
