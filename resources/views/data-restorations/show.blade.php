<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('data-restorations.index') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-white dark:bg-gray-800 text-gray-500 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ __('Review Restoration Request') }}
                    </h2>
                    <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-sm font-medium text-gray-600 dark:text-gray-300">#{{ $dataRestoration->id }}</span>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Requested by') }} <span class="font-semibold text-gray-900 dark:text-gray-200">{{ $dataRestoration->user->name ?? 'Unknown' }}</span> {{ __('on') }} {{ $dataRestoration->created_at->format('F d, Y h:i A') }}
                </p>
            </div>
            <div class="ml-auto">
                @if($dataRestoration->status === 'pending')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-50 dark:bg-yellow-900/30 px-3 py-1.5 text-sm font-semibold text-yellow-800 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-900/50">
                        <span class="h-2 w-2 rounded-full bg-yellow-500 animate-pulse"></span>
                        {{ __('Pending Review') }}
                    </span>
                @elseif($dataRestoration->status === 'approved')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                        {{ __('Approved') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 dark:bg-red-900/30 px-3 py-1.5 text-sm font-semibold text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/50">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                        {{ __('Rejected') }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data to Restore Card -->
            <div class="overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80 px-6 py-5 sm:px-8">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-farm-100 dark:bg-farm-900/30 text-farm-600 dark:text-farm-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white">{{ __('Data Payload') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('The exact record that will be recovered if approved.') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-6 sm:px-8">
                    @if($item)
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-900">
                            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                                        <span class="text-xl font-bold text-gray-600 dark:text-gray-300">{{ substr(class_basename($dataRestoration->model_type), 0, 1) }}</span>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $item->name ?? __('Unknown Record') }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                            {{ class_basename($dataRestoration->model_type) }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">ID: #{{ $item->id }}</span>
                                    </div>
                                </div>
                            </div>

                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                @if(class_basename($dataRestoration->model_type) === 'Product')
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('SKU') }}</dt>
                                        <dd class="mt-2"><span class="inline-flex items-center rounded-lg bg-gray-50 dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-900 dark:text-gray-200 border border-gray-200 dark:border-gray-700">{{ $item->sku }}</span></dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Price') }}</dt>
                                        <dd class="mt-2 text-lg font-bold text-gray-900 dark:text-white">${{ number_format($item->price, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Current Stock') }}</dt>
                                        <dd class="mt-2 flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
                                            <div class="h-2 w-2 rounded-full {{ $item->stock_quantity > 0 ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                                            {{ $item->stock_quantity }} {{ __('units') }}
                                        </dd>
                                    </div>
                                @elseif(class_basename($dataRestoration->model_type) === 'Category')
                                    <div class="sm:col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Description') }}</dt>
                                        <dd class="mt-2 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $item->description ?? 'N/A' }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @else
                        <div class="rounded-xl bg-red-50 dark:bg-red-900/20 p-6 border border-red-100 dark:border-red-900/50">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-semibold text-red-800 dark:text-red-300">{{ __('Record Permanently Lost') }}</h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-400 leading-relaxed">
                                        <p>{{ __('This record no longer exists even in the soft-deleted trash. It may have been forcefully removed from the database via command line or an external tool.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Actions & Context -->
        <div class="space-y-6">
            <!-- Manager's Reason Card -->
            <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-farm-500 to-farm-700 dark:from-farm-600 dark:to-farm-800 shadow-lg text-white">
                <div class="px-6 py-6 sm:px-8">
                    <div class="flex items-center gap-3 mb-4 opacity-90">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        <h3 class="text-base font-bold uppercase tracking-wider">{{ __('Manager\'s Reason') }}</h3>
                    </div>
                    <blockquote class="text-lg font-medium leading-relaxed italic text-farm-50 dark:text-farm-100">
                        "{{ $dataRestoration->reason }}"
                    </blockquote>
                    <div class="mt-4 pt-4 border-t border-farm-400/30">
                        <p class="text-sm font-medium text-farm-200">
                            {{ __('Submitted by') }} {{ $dataRestoration->user->name ?? 'Unknown' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Area -->
            @if($dataRestoration->status === 'pending' && $item)
                <div class="overflow-hidden rounded-3xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <div class="px-6 py-6 sm:px-8 text-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Make a Decision') }}</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 mb-6">{{ __('Approving this will instantly restore the record and make it visible again across the platform.') }}</p>
                        
                        <div class="space-y-3">
                            <form action="{{ route('data-restorations.update', $dataRestoration) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3.5 text-sm font-bold text-white shadow-md shadow-emerald-500/20 hover:bg-emerald-500 hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    {{ __('Approve & Restore') }}
                                </button>
                            </form>
                            <form action="{{ route('data-restorations.update', $dataRestoration) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-2xl bg-white dark:bg-gray-800 px-4 py-3.5 text-sm font-bold text-red-600 dark:text-red-400 shadow-sm ring-1 ring-inset ring-red-200 dark:ring-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    {{ __('Reject Request') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif($dataRestoration->status !== 'pending')
                <div class="overflow-hidden rounded-3xl bg-gray-50 dark:bg-gray-800/50 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <div class="px-6 py-6 sm:px-8">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                                <svg class="h-4 w-4 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Resolution') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ __('This request was') }} 
                            <strong class="{{ $dataRestoration->status === 'approved' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ strtoupper($dataRestoration->status) }}
                            </strong>
                            {{ __('by') }} <strong class="text-gray-900 dark:text-white">{{ $dataRestoration->admin->name ?? 'System' }}</strong>
                            {{ __('on') }} {{ $dataRestoration->updated_at->format('F d, Y') }} {{ __('at') }} {{ $dataRestoration->updated_at->format('h:i A') }}.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
