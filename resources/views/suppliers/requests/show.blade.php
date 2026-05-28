<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('supplier-profile-requests.index') }}" class="inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ __('Review Profile Edit Request #PREQ-') }}{{ str_pad($supplierProfileRequest->id, 4, '0', STR_PAD_LEFT) }}
                    </h2>
                    @php
                        $statusClass = match($supplierProfileRequest->status) {
                            'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-red-100 text-red-700 border-red-200',
                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                        {{ ucfirst($supplierProfileRequest->status) }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">
                    {{ __('Submitted by ') }} <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $supplierProfileRequest->supplier->name }}</span> {{ __(' on ') }} {{ $supplierProfileRequest->created_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-6">
        @if ($supplierProfileRequest->status === 'rejected')
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-red-800">{{ __('Request Rejected') }}</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ $supplierProfileRequest->rejection_reason }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Original Data (History) -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60 overflow-hidden flex flex-col">
                <div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Previous Information') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('The supplier data at the time of the request.') }}</p>
                    </div>
                    <span class="inline-flex rounded-full bg-gray-200 dark:bg-gray-700 p-1.5 text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </span>
                </div>
                <div class="p-6 space-y-6 flex-1">
                    @php $original = $supplierProfileRequest->original_data; @endphp
                    
                    @if(!empty($original['profile_picture']))
                        <div class="flex items-center gap-4">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($original['profile_picture']) }}" alt="Old Logo" class="h-16 w-16 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700">
                            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Previous Logo') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-x-4 gap-y-5">
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Company Name') }}</span>
                            <span class="mt-1 block text-sm font-medium text-gray-900 dark:text-white">{{ $original['name'] ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Contact Person') }}</span>
                            <span class="mt-1 block text-sm font-medium text-gray-900 dark:text-white">{{ $original['contact_person'] ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Phone') }}</span>
                            <span class="mt-1 block text-sm font-medium text-gray-900 dark:text-white">{{ $original['phone'] ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Email') }}</span>
                            <span class="mt-1 block text-sm font-medium text-gray-900 dark:text-white">{{ $original['email'] ?? '—' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Full Address') }}</span>
                            <span class="mt-1 block text-sm font-medium text-gray-900 dark:text-white">{{ $original['address'] ?? '—' }}</span>
                        </div>
                    </div>

                    @if(!empty($original['contacts']) && count($original['contacts']) > 0)
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-5">
                            <span class="block text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">{{ __('Additional Contacts') }}</span>
                            <div class="space-y-3">
                                @foreach($original['contacts'] as $contact)
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-sm">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $contact['name'] ?: 'Unnamed' }} <span class="text-xs font-normal text-gray-500">({{ $contact['role'] ?: 'No Role' }})</span></p>
                                        <p class="text-gray-600 dark:text-gray-400 mt-0.5">{{ $contact['email'] ?: 'No Email' }} • {{ $contact['phone'] ?: 'No Phone' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Requested Changes -->
            <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-farm-200/60 dark:ring-farm-700/60 overflow-hidden border-2 border-transparent relative flex flex-col {{ $supplierProfileRequest->status === 'pending' ? 'ring-2 ring-farm-500 shadow-md' : '' }}">
                <div class="bg-farm-50 dark:bg-farm-900/30 border-b border-farm-100 dark:border-farm-800 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-farm-700 dark:text-farm-400">{{ __('Requested Changes') }}</h3>
                        <p class="text-xs text-farm-600 dark:text-farm-500">{{ __('The new proposed information.') }}</p>
                    </div>
                    <span class="inline-flex rounded-full bg-farm-200 dark:bg-farm-800 p-1.5 text-farm-700 dark:text-farm-400">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </span>
                </div>
                <div class="p-6 space-y-6 flex-1">
                    @php 
                        $requested = $supplierProfileRequest->requested_changes;
                        $highlightClass = "text-farm-700 dark:text-farm-400 font-bold bg-farm-50 dark:bg-farm-900/40 rounded px-1 -mx-1";
                    @endphp

                    @if(!empty($requested['profile_picture']))
                        <div class="flex items-center gap-4">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($requested['profile_picture']) }}" alt="New Logo" class="h-16 w-16 rounded-full object-cover ring-2 ring-farm-500">
                            <span class="text-sm font-semibold text-farm-600 dark:text-farm-400">{{ __('New Logo Uploaded') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-x-4 gap-y-5">
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Company Name') }}</span>
                            <span class="mt-1 block text-sm font-medium {{ ($original['name'] ?? '') !== ($requested['name'] ?? '') ? $highlightClass : 'text-gray-900 dark:text-white' }}">{{ $requested['name'] ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Contact Person') }}</span>
                            <span class="mt-1 block text-sm font-medium {{ ($original['contact_person'] ?? '') !== ($requested['contact_person'] ?? '') ? $highlightClass : 'text-gray-900 dark:text-white' }}">{{ $requested['contact_person'] ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Phone') }}</span>
                            <span class="mt-1 block text-sm font-medium {{ ($original['phone'] ?? '') !== ($requested['phone'] ?? '') ? $highlightClass : 'text-gray-900 dark:text-white' }}">{{ $requested['phone'] ?? '—' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Email') }}</span>
                            <span class="mt-1 block text-sm font-medium {{ ($original['email'] ?? '') !== ($requested['email'] ?? '') ? $highlightClass : 'text-gray-900 dark:text-white' }}">{{ $requested['email'] ?? '—' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Full Address') }}</span>
                            <span class="mt-1 block text-sm font-medium {{ ($original['address'] ?? '') !== ($requested['address'] ?? '') ? $highlightClass : 'text-gray-900 dark:text-white' }}">{{ $requested['address'] ?? '—' }}</span>
                        </div>
                    </div>

                    @if(!empty($requested['contacts']) && count($requested['contacts']) > 0)
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-5">
                            <span class="block text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">{{ __('Additional Contacts') }}</span>
                            <div class="space-y-3">
                                @foreach($requested['contacts'] as $contact)
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 text-sm">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $contact['name'] ?: 'Unnamed' }} <span class="text-xs font-normal text-gray-500">({{ $contact['role'] ?: 'No Role' }})</span></p>
                                        <p class="text-gray-600 dark:text-gray-400 mt-0.5">{{ $contact['email'] ?: 'No Email' }} • {{ $contact['phone'] ?: 'No Phone' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        @if($supplierProfileRequest->status === 'pending')
            <div class="rounded-2xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Admin Action') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Please review the requested changes against the original data. Approving will update the supplier profile immediately.') }}</p>
                
                <div class="mt-6 flex flex-wrap gap-4" x-data>
                    <form action="{{ route('supplier-profile-requests.update', $supplierProfileRequest) }}" method="POST" class="inline-block" x-ref="approveForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="approved">
                        
                        <!-- Confirmation Modal for Approval -->
                        <x-modal name="confirm-approve-request" focusable>
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Confirm Approval') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Are you sure you want to approve these changes? The supplier profile will be overwritten with the requested information.') }}
                                </p>
                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-secondary-button>
                                    <x-primary-button x-on:click="$refs.approveForm.submit()">{{ __('Yes, Approve Changes') }}</x-primary-button>
                                </div>
                            </div>
                        </x-modal>

                        <button type="button" x-on:click.prevent="$dispatch('open-modal', 'confirm-approve-request')" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ __('Approve Changes') }}
                        </button>
                    </form>

                    <div x-data="{ openReject: false }" class="relative inline-block">
                        <button type="button" @click="openReject = !openReject" class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/30 px-4 py-2.5 text-sm font-semibold text-red-700 dark:text-red-400 shadow-sm hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            {{ __('Reject Request') }}
                        </button>
                        
                        <div x-show="openReject" @click.away="openReject = false" class="absolute bottom-full left-0 mb-2 w-80 rounded-xl bg-white dark:bg-gray-800 p-4 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700" x-transition x-cloak>
                            <form action="{{ route('supplier-profile-requests.update', $supplierProfileRequest) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <div>
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Reason for rejection') }}</label>
                                    <textarea id="rejection_reason" name="rejection_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Provide a reason for the supplier..." required></textarea>
                                </div>
                                <div class="mt-3 flex justify-end gap-2">
                                    <button type="button" @click="openReject = false" class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancel') }}</button>
                                    <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">{{ __('Confirm Rejection') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
