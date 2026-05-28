<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Review Edit Request') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Compare current values vs requested changes and approve or reject.') }}</p>
            </div>
            <a href="{{ route('supplier-requests.index') }}" class="btn-secondary">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in mx-auto max-w-4xl space-y-6">

        @php
            $product = $productEditRequest->product;
            $changes = $productEditRequest->requested_changes ?? [];
            $fieldLabels = ['name' => 'Name', 'description' => 'Description', 'price' => 'Price', 'unit' => 'Unit'];
        @endphp

        {{-- Request Meta --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Submitted By</p>
                <p class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $productEditRequest->user?->name ?? '—' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $productEditRequest->supplier?->name ?? '—' }}</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Submitted On</p>
                <p class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $productEditRequest->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $productEditRequest->created_at->format('h:i A') }}</p>
            </div>
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Status</p>
                @php
                    $cls = match($productEditRequest->status) {
                        'pending'  => 'bg-yellow-100 text-yellow-700',
                        'approved' => 'bg-green-100 text-green-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        default    => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                    };
                @endphp
                <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-sm font-bold {{ $cls }}">
                    {{ ucfirst($productEditRequest->status) }}
                </span>
                @if($productEditRequest->reviewed_at)
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Reviewed {{ $productEditRequest->reviewed_at->format('M d, Y') }} by {{ $productEditRequest->reviewer?->name }}</p>
                @endif
            </div>
        </div>

        {{-- Supplier Reason --}}
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">{{ __('Reason for Edit') }}</h3>
            <p class="text-sm text-gray-700 dark:text-gray-200 leading-relaxed">{{ $productEditRequest->reason }}</p>
        </div>

        {{-- Side-by-side diff --}}
        <div class="rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 overflow-hidden">
            <div class="grid grid-cols-2 divide-x divide-gray-100 dark:divide-gray-700">
                <div class="p-6">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-4 uppercase tracking-wider">Current Values</h3>
                    <div class="space-y-4">
                        @foreach($fieldLabels as $field => $label)
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ $label }}</p>
                                <p class="mt-1 text-sm {{ array_key_exists($field, $changes) ? 'text-gray-400 dark:text-gray-500 line-through' : 'font-medium text-gray-900 dark:text-white' }}">
                                    {{ $field === 'price' ? '₱' . number_format($product->$field ?? 0, 2) : ($product->$field ?? '—') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-green-50/40 dark:bg-green-950/10 p-6">
                    <h3 class="text-sm font-bold text-green-700 dark:text-green-400 mb-4 uppercase tracking-wider">Requested Changes</h3>
                    <div class="space-y-4">
                        @foreach($fieldLabels as $field => $label)
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ $label }}</p>
                                @if(array_key_exists($field, $changes))
                                    <p class="mt-1 text-sm font-bold text-green-700">
                                        {{ $field === 'price' ? '₱' . number_format($changes[$field], 2) : $changes[$field] }}
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-gray-400 dark:text-gray-500 italic">No change</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Reviewer Note (if already reviewed) --}}
        @if(!$productEditRequest->isPending() && $productEditRequest->reviewer_note)
        <div class="rounded-2xl border {{ $productEditRequest->isApproved() ? 'border-green-200 bg-green-50 dark:border-green-900/40 dark:bg-green-950/20' : 'border-red-200 bg-red-50 dark:border-red-900/40 dark:bg-red-950/20' }} p-5">
            <p class="text-xs font-semibold uppercase tracking-wider {{ $productEditRequest->isApproved() ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $productEditRequest->isApproved() ? 'Approval Note' : 'Rejection Reason' }}
            </p>
            <p class="mt-1 text-sm text-gray-800 dark:text-gray-100">{{ $productEditRequest->reviewer_note }}</p>
        </div>
        @endif

        {{-- Approve / Reject Actions (only if pending) --}}
        @if(trim(strtolower($productEditRequest->status)) === 'pending')
        <div class="grid gap-4 sm:grid-cols-2">
            {{-- Approve --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-green-200 dark:ring-green-900/50">
                <h3 class="text-base font-bold text-green-800 dark:text-green-400 mb-1">✅ Approve</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-4">This will immediately apply the changes to the product.</p>
                <form method="POST" action="{{ route('supplier-requests.update', $productEditRequest) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="approve">
                    <div class="mb-4">
                        <x-input-label for="approve_note" :value="__('Note (optional)')" />
                        <textarea id="approve_note" name="reviewer_note" rows="2"
                            class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-green-500 focus:ring-green-500 text-sm"
                            placeholder="e.g. Verified against updated contract..."></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-green-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-green-700 transition-colors">
                        Approve &amp; Apply Changes
                    </button>
                </form>
            </div>

            {{-- Reject --}}
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-red-200 dark:ring-red-900/50">
                <h3 class="text-base font-bold text-red-800 dark:text-red-400 mb-1">❌ Reject</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-4">The supplier will be notified with your rejection reason.</p>
                <form method="POST" action="{{ route('supplier-requests.update', $productEditRequest) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="reject">
                    <div class="mb-4">
                        <x-input-label for="reject_note" :value="__('Reason for Rejection *')" />
                        <textarea id="reject_note" name="reviewer_note" rows="2" required
                            class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-red-500 focus:ring-red-500 text-sm"
                            placeholder="e.g. Price change not aligned with current agreement..."></textarea>
                        <x-input-error :messages="$errors->get('reviewer_note')" class="mt-2" />
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition-colors">
                        Reject Request
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 p-5 text-center text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">
            This request has already been <strong>{{ ucfirst($productEditRequest->status) }}</strong> and cannot be reviewed again.
        </div>
        @endif

    </div>
</x-app-layout>
