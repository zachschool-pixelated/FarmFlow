<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Review Stock Request') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Request #{{ $stockRequest->id }} for {{ $stockRequest->product->name }}</p>
            </div>
            <a href="{{ route('stock-requests.index') }}" class="btn-secondary">{{ __('Back to Requests') }}</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl animate-fade-in space-y-6">
        <!-- Request Details -->
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 px-6 py-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Request Information</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Product</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $stockRequest->product->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $stockRequest->product->category?->name ?? 'Uncategorized' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Quantity Requested</dt>
                        <dd class="mt-1 text-sm font-bold text-farm-600 dark:text-farm-400">{{ $stockRequest->quantity_requested }} units</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Date Requested</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">{{ $stockRequest->created_at->format('F d, Y h:i A') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Notes from Manager</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-300">
                            @if($stockRequest->notes)
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-3 italic border border-gray-100 dark:border-gray-800">
                                    "{{ $stockRequest->notes }}"
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">No notes provided.</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Supplier Action Form -->
        @if(auth()->user()->isSupplier())
            <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 px-6 py-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Supplier Decision</h3>
                </div>
                
                @if(in_array($stockRequest->status, ['completed', 'rejected']))
                    <div class="p-6 text-center">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            This request is currently <strong class="text-gray-900 dark:text-white">{{ ucfirst($stockRequest->status) }}</strong> and cannot be modified.
                        </p>
                    </div>
                @else
                    <div class="p-6" x-data="{ 
                        selectedStatus: '{{ old('status', $stockRequest->status) }}',
                        init() {
                            if (this.$refs.datePicker) {
                                flatpickr(this.$refs.datePicker, {
                                    dateFormat: 'Y-m-d',
                                    minDate: 'today'
                                });
                            }
                        }
                    }">
                        <form id="supplier-decision-form" method="POST" action="{{ route('stock-requests.update', $stockRequest) }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Update Status</label>
                                <select id="status" name="status" x-model="selectedStatus" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 sm:text-sm">
                                    <option value="pending" @selected($stockRequest->status == 'pending')>Pending (Reviewing)</option>
                                    <option value="processing" @selected($stockRequest->status == 'processing')>Accept & Processing</option>
                                    <option value="shipped" @selected($stockRequest->status == 'shipped')>Shipped</option>
                                    <option value="rejected" @selected($stockRequest->status == 'rejected')>Reject Request</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div x-show="selectedStatus === 'processing' || selectedStatus === 'shipped'" x-cloak>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected Delivery (ETA)</label>
                                <div class="mt-1">
                                    @if($stockRequest->expected_delivery_at)
                                        <div class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-semibold">{{ $stockRequest->expected_delivery_at->format('M d, Y') }}</span>
                                            <span class="inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-700 px-2 py-0.5 text-[10px] font-bold tracking-wide text-gray-600 dark:text-gray-400">LOCKED</span>
                                            <input type="hidden" name="expected_delivery_at" value="{{ $stockRequest->expected_delivery_at->format('Y-m-d') }}">
                                        </div>
                                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">The ETA was already provided and is locked.</p>
                                    @else
                                        <input type="text" x-ref="datePicker" name="expected_delivery_at" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500 sm:text-sm" placeholder="Select expected delivery date..." :required="selectedStatus === 'processing' || selectedStatus === 'shipped'">
                                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Please provide a date so the manager knows when to expect the stock.</p>
                                    @endif
                                </div>
                                <x-input-error :messages="$errors->get('expected_delivery_at')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700 pt-5">
                                <a href="{{ route('stock-requests.index') }}" class="btn-secondary">Cancel</a>
                                <x-primary-button type="button" x-on:click.prevent="$dispatch('open-modal', 'confirm-supplier-decision')">Save Decision</x-primary-button>
                            </div>
                        </form>

                        <x-modal name="confirm-supplier-decision" focusable>
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Confirm Decision') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Are you sure you want to save this decision? 
                                    <span x-show="selectedStatus === 'processing' || selectedStatus === 'shipped'">If you are setting a new ETA date, it will be <strong>locked</strong> and cannot be changed later.</span>
                                </p>
                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </x-secondary-button>
                                    <x-primary-button x-on:click="document.getElementById('supplier-decision-form').submit()">
                                        {{ __('Confirm & Save') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </x-modal>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
