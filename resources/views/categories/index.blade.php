<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Categories') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Organize farm supply products into groups.') }}</p>
            </div>
            <a href="{{ route('categories.create') }}" class="page-header-btn">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ __('New Category') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-4" x-data="{ viewMode: localStorage.getItem('categoryViewMode') || 'list' }" x-init="$watch('viewMode', val => localStorage.setItem('categoryViewMode', val))">
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
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Description') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Products') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($categories as $category)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $category->description ?: '—' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-lg bg-farm-100 px-2.5 py-1 text-xs font-semibold text-farm-800">
                                        {{ $category->products_count ?? $category->products->count() ?? 0 }}
                                    </span>
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
                                                <a href="{{ route('categories.show', $category) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900">
                                                    <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                    {{ __('View Category') }}
                                                </a>
                                                @if(($category->products_count ?? $category->products->count() ?? 0) == 0)
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-red-600 hover:bg-red-50">
                                                        <svg class="mr-3 h-4 w-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                        {{ __('Delete Category') }}
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                        </div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No categories found.') }}</p>
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
            @forelse ($categories as $category)
                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 hover:shadow-md transition-all">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-farm-100 dark:bg-farm-900/30 text-farm-600 dark:text-farm-400">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            </div>
                            
                            <!-- Action Dropdown -->
                            <div x-data="{ dropdownOpen: false }" class="relative">
                                <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="inline-flex items-center justify-center rounded-lg p-1.5 text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-700 dark:text-gray-200 transition-colors">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" /></svg>
                                </button>
                                <div x-show="dropdownOpen" x-transition class="absolute right-0 top-8 z-50 w-48 rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" x-cloak>
                                    <div class="py-1 text-left text-sm">
                                        <a href="{{ route('categories.show', $category) }}" class="flex w-full items-center px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900">
                                            <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            {{ __('View Category') }}
                                        </a>
                                        @if(($category->products_count ?? $category->products->count() ?? 0) == 0)
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex w-full items-center px-4 py-2 text-red-600 hover:bg-red-50">
                                                <svg class="mr-3 h-4 w-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                {{ __('Delete Category') }}
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-1" title="{{ $category->name }}">{{ $category->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2" title="{{ $category->description }}">{{ $category->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50/50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700 px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-farm-600 dark:text-farm-400">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            {{ $category->products_count ?? $category->products->count() ?? 0 }} {{ Str::plural('Product', $category->products_count ?? $category->products->count() ?? 0) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl bg-white dark:bg-gray-800 p-16 text-center shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                        <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No categories found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
        {{ $categories->links() }}
    </div>
    </div>
</x-app-layout>