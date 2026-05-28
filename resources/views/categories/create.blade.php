<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Create Category') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Add a new farm supply grouping.') }}</p>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="mx-auto max-w-2xl">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full rounded-xl" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="description">
                            {{ __('Description') }} <span class="text-xs text-gray-500 dark:text-gray-400 font-normal ml-1">({{ __('Optional') }})</span>
                        </x-input-label>
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <x-primary-button>{{ __('Save Category') }}</x-primary-button>
                        <a href="{{ route('categories.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>