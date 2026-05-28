<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FarmFlow') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Flatpickr (Modern Datepicker) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dark Mode FOUC Prevention -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-farm-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
        <div class="min-h-screen" x-data="{ sidebarOpen: false }">
            @include('layouts.navigation')

            <!-- Main Content Area (offset for sidebar) -->
            <div class="lg:pl-72 pt-16 lg:pt-0">
                <!-- Page Heading -->
                <!-- Page Heading -->
                @isset($header)
                    <header class="sticky top-0 z-30 border-b border-gray-200/80 dark:border-gray-700/80 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md lg:top-0 transition-colors duration-200">
                        <div class="px-4 py-5 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                {{ $header }}
                            </div>

                            @php
                                $notifications = collect();
                                $unreadCount = 0;
                                
                                if (auth()->check()) {
                                    if (!auth()->user()->isSupplier()) {
                                        // Manager Notifications: Arriving Today or Overdue Delivery Requests
                                        $deliveries = collect();
                                        if (auth()->user()->role === 'manager') {
                                            $deliveries = \App\Models\StockRequest::with('product')
                                                ->whereIn('status', ['processing', 'shipped'])
                                                ->whereNotNull('expected_delivery_at')
                                                ->whereDate('expected_delivery_at', '<=', now())
                                                ->get()
                                                ->map(function ($notif) {
                                                    return [
                                                        'type' => 'delivery',
                                                        'title' => $notif->product->name,
                                                        'message' => $notif->expected_delivery_at->isToday() ? 'Delivery arriving today' : 'Delivery overdue',
                                                        'badge_text' => $notif->expected_delivery_at->isToday() ? 'TODAY' : 'OVERDUE',
                                                        'badge_class' => $notif->expected_delivery_at->isToday() 
                                                            ? 'notif-badge-delivery-today' 
                                                            : 'notif-badge-delivery-overdue',
                                                        'link' => route('stock-requests.index'),
                                                        'time' => $notif->updated_at ?? $notif->created_at,
                                                    ];
                                                });
                                        }

                                        // Manager Notifications: Low Stock Alert
                                        $lowStockProducts = collect();
                                        if (auth()->user()->role === 'manager') {
                                            $lowStockProducts = \App\Models\Product::whereColumn('stock_quantity', '<=', 'reorder_level')
                                                ->whereDoesntHave('stockRequests', function ($query) {
                                                    $query->whereIn('status', ['pending', 'processing', 'shipped']);
                                                })
                                                ->get()
                                                ->map(function ($product) {
                                                    $isOut = $product->stock_quantity == 0;
                                                    return [
                                                        'type' => 'low_stock',
                                                        'title' => $product->name,
                                                        'message' => $isOut 
                                                            ? 'Out of stock (Reorder level: ' . $product->reorder_level . ')' 
                                                            : 'Low stock: ' . $product->stock_quantity . ' left (Reorder level: ' . $product->reorder_level . ')',
                                                        'badge_text' => $isOut ? 'OUT' : 'LOW',
                                                        'badge_class' => $isOut 
                                                            ? 'notif-badge-out' 
                                                            : 'notif-badge-low',
                                                        'link' => route('products.index', ['filter' => 'low_stock']),
                                                        'time' => $product->updated_at ?? $product->created_at,
                                                    ];
                                                });
                                        }

                                        $productEditReqs = collect();
                                        $profileEditReqs = collect();
                                        $dataRestorations = collect();

                                        if (auth()->user()->role === 'manager') {
                                            // Product Edit Requests (Visible to Admin and Manager)
                                            $productEditReqs = \App\Models\ProductEditRequest::where('status', 'pending')
                                                ->with(['product', 'supplier'])
                                                ->get()
                                                ->map(function ($req) {
                                                    return [
                                                        'type' => 'product_edit_request',
                                                        'title' => "Product Edit: " . ($req->product->name ?? 'Unknown'),
                                                        'message' => "Requested by " . ($req->supplier->name ?? 'Supplier') . ": " . \Illuminate\Support\Str::limit($req->reason, 40),
                                                        'badge_text' => 'EDIT',
                                                        'badge_class' => 'notif-badge-edit',
                                                        'link' => route('supplier-requests.index'),
                                                        'time' => $req->created_at,
                                                    ];
                                                });

                                            // Supplier Profile Edit Requests (Visible to Admin and Manager)
                                            $profileEditReqs = \App\Models\SupplierProfileEditRequest::where('status', 'pending')
                                                ->with('supplier')
                                                ->get()
                                                ->map(function ($req) {
                                                    return [
                                                        'type' => 'supplier_profile_edit_request',
                                                        'title' => "Profile Edit: " . ($req->supplier->name ?? 'Supplier'),
                                                        'message' => "Changes pending approval",
                                                        'badge_text' => 'PROFILE',
                                                        'badge_class' => 'notif-badge-profile',
                                                        'link' => route('supplier-profile-requests.index'),
                                                        'time' => $req->created_at,
                                                    ];
                                                });
                                        }

                                        if (auth()->user()->role === 'admin') {
                                            // Data Restoration Requests (Visible to Admin only)
                                            $dataRestorations = \App\Models\DataRestorationRequest::where('status', 'pending')
                                                ->with('user')
                                                ->get()
                                                ->map(function ($req) {
                                                    $modelName = class_basename($req->model_type);
                                                    return [
                                                        'type' => 'data_restoration_request',
                                                        'title' => "Restore: " . $modelName,
                                                        'message' => "Requested by " . ($req->user->name ?? 'User') . ": " . \Illuminate\Support\Str::limit($req->reason, 40),
                                                        'badge_text' => 'RESTORE',
                                                        'badge_class' => 'notif-badge-restore',
                                                        'link' => route('data-restorations.index'),
                                                        'time' => $req->created_at,
                                                    ];
                                                });
                                        }

                                        $notifications = $deliveries
                                            ->concat($lowStockProducts)
                                            ->concat($productEditReqs)
                                            ->concat($profileEditReqs)
                                            ->concat($dataRestorations)
                                            ->sortByDesc('time');
                                        
                                        $unreadCount = $notifications->count();
                                    } else {
                                        // Supplier Notifications: New/Pending requests
                                        $notifications = \App\Models\StockRequest::with('product')
                                            ->where('supplier_id', auth()->user()->supplier_id)
                                            ->where('status', 'pending')
                                            ->get()
                                            ->map(function ($notif) {
                                                return [
                                                    'type' => 'supplier_request',
                                                    'title' => $notif->product->name,
                                                    'message' => 'New request: ' . $notif->quantity_requested . ' units',
                                                    'badge_text' => 'NEW',
                                                    'badge_class' => 'notif-badge-new',
                                                    'link' => route('stock-requests.index'),
                                                    'time' => $notif->created_at,
                                                ];
                                            });
                                        $unreadCount = $notifications->count();
                                    }
                                }
                            @endphp

                            <!-- Notification Bell -->
                            <div class="relative flex-shrink-0" x-data="{ open: false }">
                                <style>
                                    .notif-badge {
                                        display: inline-flex;
                                        align-items: center;
                                        justify-content: center;
                                        border-radius: 9999px;
                                        padding: 2px 6px !important;
                                        font-size: 9px !important;
                                        font-weight: 700 !important;
                                        line-height: 1 !important;
                                        text-transform: uppercase;
                                        letter-spacing: 0.05em;
                                    }
                                    .notif-badge-delivery-today { background-color: #fef08a !important; color: #854d0e !important; }
                                    .dark .notif-badge-delivery-today { background-color: rgba(250, 204, 21, 0.15) !important; color: #facc15 !important; }
                                    .notif-badge-delivery-overdue { background-color: #fee2e2 !important; color: #991b1b !important; }
                                    .dark .notif-badge-delivery-overdue { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
                                    .notif-badge-low { background-color: #fef3c7 !important; color: #92400e !important; }
                                    .dark .notif-badge-low { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
                                    .notif-badge-out { background-color: #fee2e2 !important; color: #991b1b !important; }
                                    .dark .notif-badge-out { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
                                    .notif-badge-edit { background-color: #e0e7ff !important; color: #3730a3 !important; }
                                    .dark .notif-badge-edit { background-color: rgba(99, 102, 241, 0.15) !important; color: #818cf8 !important; }
                                    .notif-badge-profile { background-color: #f3e8ff !important; color: #6b21a8 !important; }
                                    .dark .notif-badge-profile { background-color: rgba(168, 85, 247, 0.15) !important; color: #c084fc !important; }
                                    .notif-badge-restore { background-color: #ccfbf1 !important; color: #075985 !important; }
                                    .dark .notif-badge-restore { background-color: rgba(20, 184, 166, 0.15) !important; color: #2dd4bf !important; }
                                    .notif-badge-new { background-color: #dbeafe !important; color: #1e40af !important; }
                                    .dark .notif-badge-new { background-color: rgba(59, 130, 246, 0.15) !important; color: #60a5fa !important; }
                                </style>
                                <button @click="open = !open" @click.away="open = false" type="button" class="relative inline-flex items-center justify-center rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                    @if($unreadCount > 0)
                                        <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                        </span>
                                    @endif
                                </button>
 
                                <div x-show="open" x-transition.opacity x-cloak class="absolute right-0 mt-2 w-80 origin-top-right rounded-2xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10 overflow-hidden z-50">
                                    <div class="border-b border-gray-100 dark:border-gray-700 px-4 py-3">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Notifications') }}</h3>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        @forelse($notifications as $notif)
                                            <a href="{{ $notif['link'] }}" class="block border-b border-gray-50 dark:border-gray-700/50 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $notif['title'] }}</p>
                                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $notif['message'] }}</p>
                                                    </div>
                                                    <span class="notif-badge {{ $notif['badge_class'] }} whitespace-nowrap">
                                                        {{ $notif['badge_text'] }}
                                                    </span>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('No new notifications.') }}
                                            </div>
                                        @endforelse
                                    </div>
                                    @if($unreadCount > 0)
                                        <div class="bg-gray-50 dark:bg-gray-900/50 p-2.5 text-center border-t border-gray-100 dark:border-gray-700">
                                            <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-farm-600 dark:text-farm-400 hover:underline">
                                                View all notifications
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
