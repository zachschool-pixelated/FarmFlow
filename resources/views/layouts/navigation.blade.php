    <!-- Mobile top bar -->
    <div class="fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between bg-farm-800 dark:bg-gray-900 px-4 shadow-lg lg:hidden transition-colors duration-200">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <x-application-logo class="h-8 w-8 text-farm-300" />
            <span class="text-lg font-bold text-white tracking-tight">FarmFlow</span>
        </a>

        <button type="button" @click="sidebarOpen = true" class="inline-flex items-center justify-center rounded-lg p-2 text-white/70 hover:bg-white/10 hover:text-white transition-colors duration-200">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full transition-transform duration-300 ease-out lg:translate-x-0 bg-gradient-to-b from-farm-800 to-farm-700 dark:from-gray-900 dark:to-gray-800"
           :class="{ 'translate-x-0': sidebarOpen }">
        <div class="flex h-full flex-col">
            <!-- Logo area -->
            <div class="flex h-16 items-center justify-between px-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <x-application-logo class="h-9 w-9 text-farm-300" />
                    <div>
                        <span class="text-lg font-bold text-white tracking-tight">FarmFlow</span>
                        <span class="block text-[10px] font-medium text-farm-400 uppercase tracking-widest">Supply Management</span>
                    </div>
                </a>

                <button type="button" @click="sidebarOpen = false" class="inline-flex items-center justify-center rounded-lg p-1.5 text-white/60 hover:bg-white/10 hover:text-white lg:hidden transition-colors duration-200">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Divider -->
            <div class="mx-5 border-t border-white/10"></div>

            <!-- Navigation links -->
            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
                <p class="mb-3 px-3 text-[10px] font-semibold uppercase tracking-widest text-white/40">Main Menu</p>

                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" :icon="'<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><rect x=&quot;3&quot; y=&quot;3&quot; width=&quot;7&quot; height=&quot;7&quot; rx=&quot;1&quot;/><rect x=&quot;14&quot; y=&quot;3&quot; width=&quot;7&quot; height=&quot;7&quot; rx=&quot;1&quot;/><rect x=&quot;3&quot; y=&quot;14&quot; width=&quot;7&quot; height=&quot;7&quot; rx=&quot;1&quot;/><rect x=&quot;14&quot; y=&quot;14&quot; width=&quot;7&quot; height=&quot;7&quot; rx=&quot;1&quot;/></svg>'">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                @if(auth()->check() && auth()->user()->role !== 'admin')
                <x-responsive-nav-link :href="route('stock-requests.index')" :active="request()->routeIs('stock-requests.*') && request()->query('status') !== 'completed'" :icon="'<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><path d=&quot;M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z&quot;/><polyline points=&quot;14 2 14 8 20 8&quot;/><line x1=&quot;16&quot; y1=&quot;13&quot; x2=&quot;8&quot; y2=&quot;13&quot;/><line x1=&quot;16&quot; y1=&quot;17&quot; x2=&quot;8&quot; y2=&quot;17&quot;/><polyline points=&quot;10 9 9 9 8 9&quot;/></svg>'">
                    <span class="flex items-center justify-between w-full">
                        {{ __('Stock Requests') }}
                        @if(auth()->check() && auth()->user()->isSupplier())
                            @php
                                $pendingCount = \App\Models\StockRequest::where('supplier_id', auth()->user()->supplier_id)
                                    ->whereIn('status', ['pending', 'processing'])
                                    ->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="ml-auto inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-yellow-400 px-1.5 text-[10px] font-bold text-yellow-900">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        @endif
                    </span>
                </x-responsive-nav-link>
                @endif

                @if(auth()->check() && auth()->user()->isSupplier())
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" :icon="'<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><path d=&quot;M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z&quot;/><polyline points=&quot;3.27 6.96 12 12.01 20.73 6.96&quot;/><line x1=&quot;12&quot; y1=&quot;22.08&quot; x2=&quot;12&quot; y2=&quot;12&quot;/></svg>'">
                    {{ __('Product Catalog') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('stock-requests.index', ['status' => 'completed'])" :active="request()->query('status') === 'completed'" :icon="'<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><circle cx=&quot;12&quot; cy=&quot;12&quot; r=&quot;10&quot;/><polyline points=&quot;12 6 12 12 16 14&quot;/></svg>'">
                    {{ __('Restock History') }}
                </x-responsive-nav-link>
                @endif

                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'manager'))
                    @if(auth()->user()->role === 'manager')
                        <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" :icon="'<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><path d=&quot;M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z&quot;/></svg>'">
                            {{ __('Categories') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" :icon="'<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><path d=&quot;M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z&quot;/><polyline points=&quot;3.27 6.96 12 12.01 20.73 6.96&quot;/><line x1=&quot;12&quot; y1=&quot;22.08&quot; x2=&quot;12&quot; y2=&quot;12&quot;/></svg>'">
                            {{ __('Products') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.index') || request()->routeIs('suppliers.edit') || request()->routeIs('suppliers.create')" :icon="'<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><rect x=&quot;1&quot; y=&quot;3&quot; width=&quot;15&quot; height=&quot;13&quot; rx=&quot;2&quot;/><polygon points=&quot;16 8 20 8 23 11 23 16 16 16 16 8&quot;/><circle cx=&quot;5.5&quot; cy=&quot;18.5&quot; r=&quot;2.5&quot;/><circle cx=&quot;18.5&quot; cy=&quot;18.5&quot; r=&quot;2.5&quot;/></svg>'">
                            {{ __('Suppliers') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('suppliers.blacklisted')" :active="request()->routeIs('suppliers.blacklisted')" :icon="'<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><path d=&quot;M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z&quot;/><path d=&quot;M9 12l2 2 4-4&quot;/></svg>'">
                            {{ __('Blacklisted') }}
                        </x-responsive-nav-link>

                        <p class="mb-3 mt-6 px-3 text-[10px] font-semibold uppercase tracking-widest text-white/40">Inventory</p>

                        <x-responsive-nav-link :href="route('stock-movements.index')" :active="request()->routeIs('stock-movements.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                            </x-slot>
                            {{ __('Stock Movements') }}
                        </x-responsive-nav-link>
                    @endif

                    <p class="mb-3 mt-6 px-3 text-[10px] font-semibold uppercase tracking-widest text-white/40">Administration</p>

                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/><path d="M14 3v5h5M16 13H8M16 17H8M10 9H8"/></svg>
                        </x-slot>
                        {{ __('Reports') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </x-slot>
                        {{ __('Manage Accounts') }}
                    </x-responsive-nav-link>

                    @if(auth()->user()->role === 'manager')
                    <x-responsive-nav-link :href="route('trash.index')" :active="request()->routeIs('trash.*')">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </x-slot>
                        {{ __('Trash') }}
                    </x-responsive-nav-link>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <x-responsive-nav-link :href="route('data-restorations.index')" :active="request()->routeIs('data-restorations.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                            </x-slot>
                            <span class="flex items-center justify-between w-full gap-2">
                                <span class="truncate">{{ __('Data Restorations') }}</span>
                                @php
                                    $pendingRestorationCount = \App\Models\DataRestorationRequest::where('status', 'pending')->count();
                                @endphp
                                @if($pendingRestorationCount > 0)
                                    <span class="ml-auto flex-shrink-0 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-yellow-400 px-1.5 text-[10px] font-bold text-yellow-900">
                                        {{ $pendingRestorationCount }}
                                    </span>
                                @endif
                            </span>
                        </x-responsive-nav-link>
                    @endif

                    @if(auth()->user()->role === 'manager')
                        <x-responsive-nav-link :href="route('supplier-requests.index')" :active="request()->routeIs('supplier-requests.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </x-slot>
                            <span class="flex items-center justify-between w-full gap-2">
                                <span class="truncate">{{ __('Product Edit Requests') }}</span>
                                @php
                                    $pendingEditCount = \App\Models\ProductEditRequest::where('status', 'pending')->count();
                                @endphp
                                @if($pendingEditCount > 0)
                                    <span class="ml-auto flex-shrink-0 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-yellow-400 px-1.5 text-[10px] font-bold text-yellow-900">
                                        {{ $pendingEditCount }}
                                    </span>
                                @endif
                            </span>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('supplier-profile-requests.index')" :active="request()->routeIs('supplier-profile-requests.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </x-slot>
                            <span class="flex items-center justify-between w-full gap-2">
                                <span class="truncate">{{ __('Profile Edit Requests') }}</span>
                                @php
                                    $pendingProfileReqCount = \App\Models\SupplierProfileEditRequest::where('status', 'pending')->count();
                                @endphp
                                @if($pendingProfileReqCount > 0)
                                    <span class="ml-auto flex-shrink-0 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-yellow-400 px-1.5 text-[10px] font-bold text-yellow-900">
                                        {{ $pendingProfileReqCount }}
                                    </span>
                                @endif
                            </span>
                        </x-responsive-nav-link>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <x-responsive-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </x-slot>
                            {{ __('Audit Logs') }}
                        </x-responsive-nav-link>
                    @endif
                @endif
            </nav>

            <!-- User section -->
            <div class="border-t border-white/10 p-4">
                <div class="mb-3 flex items-center gap-3 rounded-xl bg-white/10 p-3">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-farm-300 text-farm-900 font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(Auth::user()->name, ' '), 1, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-white/50">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <button type="button" @click="
                        document.documentElement.classList.toggle('dark');
                        localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
                    " class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-white/60 hover:bg-white/10 hover:text-white transition-colors duration-200">
                        <svg class="h-4 w-4 block dark:hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                        <svg class="h-4 w-4 hidden dark:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        <span class="block dark:hidden">{{ __('Switch to Dark Mode') }}</span>
                        <span class="hidden dark:block">{{ __('Switch to Light Mode') }}</span>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm font-medium text-white/60 hover:bg-white/10 hover:text-white transition-colors duration-200">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
