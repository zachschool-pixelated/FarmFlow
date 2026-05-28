<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Create Supplier') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Add a new supply partner.') }}</p>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="mx-auto max-w-6xl">
            @include('suppliers.partials.form', [
                'supplier' => $supplier,
                'contactRows' => $contactRows,
                'formAction' => route('suppliers.store'),
                'formMethod' => 'POST',
                'submitLabel' => __('Save Supplier'),
            ])
        </div>
    </div>
</x-app-layout>