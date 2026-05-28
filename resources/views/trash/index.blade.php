<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Trash') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Recently deleted items that can be requested for restoration.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-6">
        @if($products->isEmpty() && $categories->isEmpty())
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-16 text-center shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                    <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Trash is empty.') }}</p>
            </div>
        @endif

        @if($products->isNotEmpty())
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Deleted Products') }}</h3>
                <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Product') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Category') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Deleted At') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @foreach($products as $product)
                                    <tr class="table-row-hover">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name ?? 'None' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->deleted_at->format('M d, Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <button type="button" onclick="openRestoreModal('App\\Models\\Product', {{ $product->id }})" class="text-farm-600 hover:text-farm-900 dark:text-farm-400 dark:hover:text-farm-300 font-semibold">{{ __('Request Restore') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if($categories->isNotEmpty())
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Deleted Categories') }}</h3>
                <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Category') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Deleted At') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @foreach($categories as $category)
                                    <tr class="table-row-hover">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $category->deleted_at->format('M d, Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <button type="button" onclick="openRestoreModal('App\\Models\\Category', {{ $category->id }})" class="text-farm-600 hover:text-farm-900 dark:text-farm-400 dark:hover:text-farm-300 font-semibold">{{ __('Request Restore') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Restore Request Modal -->
    <div id="restoreModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center px-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity" aria-hidden="true" onclick="closeRestoreModal()"></div>

            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div>
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-farm-100 dark:bg-farm-900/30">
                        <svg class="h-6 w-6 text-farm-600 dark:text-farm-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="modal-title">{{ __('Request Data Restoration') }}</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Please provide a reason for restoring this data. An administrator will review your request.') }}</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('trash.request-restore') }}" method="POST" class="mt-5 sm:mt-6">
                    @csrf
                    <input type="hidden" name="model_type" id="modal_model_type" value="">
                    <input type="hidden" name="model_id" id="modal_model_id" value="">
                    
                    <div class="mb-4">
                        <x-input-label for="reason" :value="__('Reason for Restoration')" />
                        <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-farm-500 focus:ring-farm-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm" required placeholder="Why should this be restored?"></textarea>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit" class="btn-primary w-full sm:w-auto">
                            {{ __('Submit Request') }}
                        </button>
                        <button type="button" class="btn-secondary w-full sm:w-auto mt-3 sm:mt-0" onclick="closeRestoreModal()">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRestoreModal(type, id) {
            document.getElementById('modal_model_type').value = type;
            document.getElementById('modal_model_id').value = id;
            document.getElementById('restoreModal').classList.remove('hidden');
        }

        function closeRestoreModal() {
            document.getElementById('restoreModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
