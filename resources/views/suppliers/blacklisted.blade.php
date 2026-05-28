<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Blacklisted Suppliers') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Manage vendors that have been restricted from the system.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Code') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Contact Person') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Phone') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Reason') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($suppliers as $supplier)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->supplier_code }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $supplier->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $supplier->contact_person ?: '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}" class="text-sm text-farm-700 hover:text-farm-800 underline decoration-farm-300 transition-colors duration-150">{{ $supplier->email }}</a>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $supplier->phone ?: '—' }}</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-medium max-w-xs truncate" title="{{ $supplier->blacklist_reason }}">
                                    {{ $supplier->blacklist_reason ?: 'No reason provided' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div x-data="{ dropdownOpen: false }" class="relative flex justify-end">
                                        <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-700 dark:text-gray-200 transition-colors">
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

                                                <form action="{{ route('suppliers.toggle-blacklist', $supplier) }}" method="POST" onsubmit="return confirm('Are you sure you want to unblacklist this supplier?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="blacklist_reason" value="">
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-emerald-600 hover:bg-emerald-50">
                                                        <svg class="mr-3 h-4 w-4 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>
                                                        </svg>
                                                        {{ __('Unblacklist Supplier') }}
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No blacklisted suppliers found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
