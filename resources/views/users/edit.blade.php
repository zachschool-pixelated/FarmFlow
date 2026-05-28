<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Edit Account') }}</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Update user details and roles.') }}</p>
    </x-slot>

    <div class="animate-fade-in mx-auto max-w-2xl">
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60" x-data="{ role: '{{ old('role', $user->role) }}' }">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" class="mt-1 block w-full rounded-xl" :value="old('name', $user->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" type="email" name="email" class="mt-1 block w-full rounded-xl" :value="old('email', $user->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div x-data="{ show: false }">
                    <x-input-label for="password" :value="__('Password')" />
                    <div class="relative mt-1">
                        <x-text-input id="password" x-bind:type="show ? 'text' : 'password'" name="password" class="block w-full rounded-xl pr-12 placeholder-gray-400" placeholder="{{ __('Leave blank to keep current password') }}" />
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center justify-center px-4 text-gray-400 dark:text-gray-500 hover:text-farm-600 focus:outline-none" :title="show ? 'Hide password' : 'Show password'">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label for="role" :value="__('Role')" />
                        <select id="role" name="role" x-model="role" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500" required>
                            @if(auth()->user()->role === 'admin')
                                <option value="manager">{{ __('Manager') }}</option>
                            @else
                                <option value="supplier">{{ __('Supplier') }}</option>
                            @endif
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <div x-show="role === 'supplier'" x-cloak>
                        <x-input-label for="supplier_id" :value="__('Assign to Supplier')" />
                        <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500" :required="role === 'supplier'">
                            <option value="">{{ __('Select Supplier') }}</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $user->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4">
                    <x-primary-button>{{ __('Update Account') }}</x-primary-button>
                    <a href="{{ route('users.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
