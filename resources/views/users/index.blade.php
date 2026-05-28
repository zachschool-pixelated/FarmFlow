<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Manage Accounts') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Manage system users, roles, and access.') }}</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ __('New Account') }}
            </a>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-700/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('User') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Role') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Association') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Password') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Date Added') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($users as $user)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-farm-100 text-farm-700 font-bold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                @if($user->is_restricted)
                                                    <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/30 px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/50">RESTRICTED</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleClass = match($user->role) {
                                            'admin' => 'bg-blue-100 text-blue-700',
                                            'supplier' => 'bg-green-100 text-green-700',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $roleClass }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->isSupplier() && $user->supplier)
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->supplier->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Supplier Access</div>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500 italic">Internal</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div x-data="{ show: false }" class="flex items-center gap-2">
                                        <span class="text-sm font-mono text-gray-900 dark:text-white" x-text="show ? '{{ $user->plain_password ?? 'Not Available' }}' : '••••••••'"></span>
                                        <button type="button" @click="show = !show" class="text-gray-400 dark:text-gray-500 hover:text-farm-600 transition-colors" :title="show ? 'Hide Password' : 'Show Password'">
                                            <svg x-show="!show" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            <svg x-show="show" x-cloak class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
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
                                            <div class="py-1">
                                                @if(auth()->user()->role === 'admin' || $user->role === 'supplier')
                                                    <a href="{{ route('users.edit', $user) }}" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:bg-gray-900">
                                                        <svg class="mr-3 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        {{ __('Edit Account') }}
                                                    </a>
                                                    @if(auth()->id() !== $user->id && $user->role !== 'admin')
                                                        <form method="POST" action="{{ route('users.toggle-restrict', $user) }}" onsubmit="return confirm('Are you sure you want to {{ $user->is_restricted ? 'unrestrict' : 'restrict' }} this account?');">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="flex w-full items-center px-4 py-2 text-sm {{ $user->is_restricted ? 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' : 'text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20' }} text-left transition-colors">
                                                                @if($user->is_restricted)
                                                                    <svg class="mr-3 h-4 w-4 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                                                                    {{ __('Unrestrict Account') }}
                                                                @else
                                                                    <svg class="mr-3 h-4 w-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                                                    {{ __('Restrict Account') }}
                                                                @endif
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <span class="flex w-full items-center px-4 py-2 text-sm text-gray-400 dark:text-gray-500 italic">
                                                        {{ __('Locked by Admin') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('No accounts found.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="border-t border-gray-100 dark:border-gray-700 px-6 py-4">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
