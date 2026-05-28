<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Suppliers') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Manage vendors and farm supply sources.') }}</p>
            </div>
            <a href="{{ route('suppliers.create') }}" class="page-header-btn">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ __('New Supplier') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-4" x-data="{ viewMode: localStorage.getItem('supplierViewMode') || 'list' }" x-init="$watch('viewMode', val => localStorage.setItem('supplierViewMode', val))">
        <!-- Toggle Header -->
        <div class="flex items-center justify-end">
            <div class="flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-1 shadow-sm">
                <button type="button" @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-gray-100 dark:bg-gray-700 text-farm-600 dark:text-farm-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'" class="rounded-md p-1.5 transition-all">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </button>
                <button type="button" @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-gray-100 dark:bg-gray-700 text-farm-600 dark:text-farm-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'" class="rounded-md p-1.5 transition-all">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                </button>
            </div>
        </div>

        <div>
            <!-- List View -->
            <div x-show="viewMode === 'list'" class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Code') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Contact Person') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Phone') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Contacts') }}</th>
                            @if(auth()->user()->role === 'admin')
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Dashboard') }}</th>
                            @endif
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($suppliers as $supplier)
                            <tr onclick="window.location='{{ route('suppliers.show', $supplier) }}'" class="table-row-hover cursor-pointer">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->supplier_code }}</td>
                                <td class="px-6 py-4">
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->name }}</span>
                                        @if($supplier->is_blacklisted)
                                            <p class="mt-1 text-xs font-medium text-red-600">{{ __('Blacklisted') }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($supplier->is_blacklisted)
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">{{ __('Blacklisted') }}</span>
                                    @elseif($supplier->is_active)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">{{ __('Active') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $supplier->contact_person ?: '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}" @click.stop class="text-sm text-farm-700 hover:text-farm-800 underline decoration-farm-300 transition-colors duration-150">{{ $supplier->email }}</a>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $supplier->phone ?: '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $supplier->contacts_count ?? 0 }}</td>
                                @if(auth()->user()->role === 'admin')
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('suppliers.dashboard', $supplier) }}" @click.stop class="action-link-edit">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h7V3H3z"/><path d="M14 3h7v7h-7z"/><path d="M14 14h7v7h-7z"/><path d="M3 14h7v7H3z"/></svg>
                                            {{ __('Dashboard') }}
                                        </a>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-right">
                                    <div x-data="{ dropdownOpen: false }" class="relative flex justify-end" @click.stop>
                                        <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 hover:text-gray-700 dark:text-gray-200 transition-colors">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                            </svg>
                                        </button>

                                        <div x-show="dropdownOpen" 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute right-4 top-10 z-50 mt-1 w-48 rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                             x-cloak>
                                            <div class="py-1 text-left text-sm">
                                                <a href="{{ route('suppliers.show', $supplier) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900">
                                                    <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                    {{ __('View Profile') }}
                                                </a>

                                                <form action="{{ route('suppliers.toggle-blacklist', $supplier) }}" method="POST" onsubmit="if({{ $supplier->is_blacklisted ? 'false' : 'true' }}) { const reason = prompt('Please enter a reason for blacklisting this supplier:'); if (!reason || reason.trim() === '') return false; this.blacklist_reason.value = reason; } return confirm('Are you sure you want to {{ $supplier->is_blacklisted ? 'unblacklist' : 'blacklist' }} this supplier?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="blacklist_reason" value="">
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 {{ $supplier->is_blacklisted ? 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900' }}">
                                                        <svg class="mr-3 h-4 w-4 {{ $supplier->is_blacklisted ? 'text-emerald-500' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            @if($supplier->is_blacklisted)
                                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>
                                                            @else
                                                                <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                                            @endif
                                                        </svg>
                                                        {{ $supplier->is_blacklisted ? __('Unblacklist Supplier') : __('Blacklist Supplier') }}
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No suppliers found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </table>
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($suppliers as $supplier)
                <div onclick="window.location='{{ route('suppliers.show', $supplier) }}'" class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 hover:shadow-md hover:ring-2 hover:ring-farm-300 dark:hover:ring-farm-600 cursor-pointer transition-all">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                @if ($supplier->profile_picture)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($supplier->profile_picture) }}" alt="{{ $supplier->name }}" class="h-12 w-12 rounded-full object-cover shadow-sm ring-2 ring-gray-50 dark:ring-gray-700/50">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-farm-100 dark:bg-farm-900/30 text-lg font-bold text-farm-600 dark:text-farm-400 shadow-sm ring-2 ring-gray-50 dark:ring-gray-700/50">
                                        {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-1" title="{{ $supplier->name }}">{{ $supplier->name }}</h3>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $supplier->supplier_code }}</span>
                                </div>
                            </div>
                            
                            <!-- Action Dropdown -->
                            <div x-data="{ dropdownOpen: false }" class="relative" @click.stop>
                                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 hover:text-gray-700 dark:text-gray-200 transition-colors">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" /></svg>
                                </button>
                                <div x-show="dropdownOpen" x-transition class="absolute right-0 top-8 z-50 w-48 rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak>
                                    <div class="py-1 text-left text-sm">
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900">
                                            <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            {{ __('View Profile') }}
                                        </a>

                                        <form action="{{ route('suppliers.toggle-blacklist', $supplier) }}" method="POST" onsubmit="if({{ $supplier->is_blacklisted ? 'false' : 'true' }}) { const reason = prompt('Please enter a reason for blacklisting this supplier:'); if (!reason || reason.trim() === '') return false; this.blacklist_reason.value = reason; } return confirm('Are you sure you want to {{ $supplier->is_blacklisted ? 'unblacklist' : 'blacklist' }} this supplier?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="blacklist_reason" value="">
                                            <button type="submit" class="flex w-full items-center px-4 py-2 {{ $supplier->is_blacklisted ? 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900' }}">
                                                <svg class="mr-3 h-4 w-4 {{ $supplier->is_blacklisted ? 'text-emerald-500' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    @if($supplier->is_blacklisted)
                                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>
                                                    @else
                                                        <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                                    @endif
                                                </svg>
                                                {{ $supplier->is_blacklisted ? __('Unblacklist') : __('Blacklist') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex flex-col gap-2">
                            @if($supplier->is_blacklisted)
                                <div><span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/30 px-2 py-1 text-xs font-semibold text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/50">BLACKLISTED</span></div>
                            @elseif($supplier->is_active)
                                <div><span class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50">ACTIVE</span></div>
                            @else
                                <div><span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-700 px-2 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">INACTIVE</span></div>
                            @endif
                            
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <span class="truncate">{{ $supplier->contact_person ?: 'No Contact Person' }}</span>
                                </div>
                                @if($supplier->email)
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        <a href="mailto:{{ $supplier->email }}" @click.stop class="truncate hover:text-farm-600 dark:hover:text-farm-400">{{ $supplier->email }}</a>
                                    </div>
                                @endif
                                @if($supplier->phone)
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <span class="truncate">{{ $supplier->phone }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50/50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700 px-5 py-3 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $supplier->contacts_count ?? 0 }} {{ Str::plural('Contact', $supplier->contacts_count ?? 0) }}</span>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('suppliers.dashboard', $supplier) }}" @click.stop class="text-xs font-semibold text-farm-600 hover:text-farm-700 dark:text-farm-400 dark:hover:text-farm-300 flex items-center gap-1">
                                {{ __('Dashboard') }}
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl bg-white dark:bg-gray-800 p-16 text-center shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                        <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No suppliers found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
        {{ $suppliers->links() }}
    </div>
</x-app-layout>