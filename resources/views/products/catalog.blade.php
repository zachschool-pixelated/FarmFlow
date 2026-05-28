<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Product Catalog') }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Products assigned to your company.') }}</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-farm-100 px-3 py-1 text-xs font-semibold text-farm-700">
                {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
            </span>
        </div>
    </x-slot>

    <div class="animate-fade-in space-y-5">

        {{-- Category Filter --}}
        @if($categories->count() > 1)
        <div class="rounded-2xl bg-white dark:bg-gray-800 p-4 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors
                          {{ !request('category_id') ? 'bg-farm-600 text-white shadow' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200' }}">
                    All Categories
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('products.index', ['category_id' => $cat->id]) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors
                          {{ request('category_id') == $cat->id ? 'bg-farm-600 text-white shadow' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200' }}">
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Summary Stats --}}
        @php
            $allProducts   = \App\Models\Product::where('supplier_id', auth()->user()->supplier_id)->get();
            $totalLow      = $allProducts->filter(fn($p) => $p->stock_quantity <= $p->reorder_level && $p->stock_quantity > 0)->count();
            $totalOut      = $allProducts->filter(fn($p) => $p->stock_quantity == 0)->count();
            $totalHealthy  = $allProducts->count() - $totalLow - $totalOut;
        @endphp
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex items-center gap-4">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">In Stock</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalHealthy }}</p>
                </div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex items-center gap-4">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-amber-100">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Low Stock</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $totalLow }}</p>
                </div>
            </div>
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-5 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex items-center gap-4">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-red-100">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Out of Stock</p>
                    <p class="text-2xl font-bold text-red-600">{{ $totalOut }}</p>
                </div>
            </div>
        </div>

        {{-- Product Cards Grid --}}
        @if($products->count() > 0)
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($products as $product)
            @php
                $isOut = $product->stock_quantity == 0;
                $isLow = !$isOut && $product->stock_quantity <= $product->reorder_level;
                $pct   = $product->reorder_level > 0
                    ? min(100, round(($product->stock_quantity / ($product->reorder_level * 2)) * 100))
                    : ($product->stock_quantity > 0 ? 100 : 0);
                $barColor = $isOut ? 'bg-red-500' : ($isLow ? 'bg-amber-400' : 'bg-emerald-500');
                // Check for any edit request on this product
                $editReq = \App\Models\ProductEditRequest::where('product_id', $product->id)
                    ->where('supplier_id', auth()->user()->supplier_id)
                    ->latest()
                    ->first();
            @endphp
            <div class="group relative rounded-2xl bg-white dark:bg-gray-800 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 p-5 flex flex-col gap-4 transition-shadow hover:shadow-md">

                {{-- Status badge --}}
                <div class="flex items-start justify-between">
                    <span class="inline-flex items-center rounded-lg bg-farm-50 px-2.5 py-1 text-xs font-semibold text-farm-700">
                        {{ $product->category?->name ?? '—' }}
                    </span>
                    @if($isOut)
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>OUT
                        </span>
                    @elseif($isLow)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>LOW
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>OK
                        </span>
                    @endif
                </div>

                {{-- Product name & unit --}}
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $product->name }}</p>
                    @if($product->description)
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500 line-clamp-2">{{ $product->description }}</p>
                    @endif
                </div>

                {{-- Stock level bar --}}
                <div>
                    <div class="mb-1.5 flex items-center justify-between text-xs">
                        <span class="font-medium text-gray-500 dark:text-gray-400 dark:text-gray-500">Stock</span>
                        <span class="font-bold {{ $isOut ? 'text-red-600' : ($isLow ? 'text-amber-600' : 'text-gray-900 dark:text-white') }}">
                            {{ $product->stock_quantity }} <span class="font-normal text-gray-400 dark:text-gray-500">/ reorder {{ $product->reorder_level }}</span>
                        </span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                        <div class="h-2 rounded-full transition-all duration-500 {{ $barColor }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Price footer --}}
                <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-3">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Unit Price</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">₱{{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Unit</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $product->unit }}</p>
                    </div>
                </div>

                {{-- Edit Request Status / Button --}}
                <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                    @if($editReq && $editReq->isPending())
                        <div class="flex items-center gap-2 rounded-xl bg-yellow-50 px-3 py-2">
                            <svg class="h-4 w-4 flex-shrink-0 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs font-semibold text-yellow-700">Edit request pending review</p>
                        </div>
                    @elseif($editReq && $editReq->isApproved())
                        <div class="rounded-xl bg-green-50 px-3 py-2">
                            <p class="text-xs font-semibold text-green-700">✅ Last edit request was approved</p>
                            @if($editReq->reviewer_note)
                                <p class="mt-0.5 text-xs text-green-600">"{{ $editReq->reviewer_note }}"</p>
                            @endif
                        </div>
                    @elseif($editReq && $editReq->isRejected())
                        <div class="rounded-xl bg-red-50 px-3 py-2 mb-2">
                            <p class="text-xs font-semibold text-red-700">❌ Last edit request was rejected</p>
                            @if($editReq->reviewer_note)
                                <p class="mt-0.5 text-xs text-red-600">"{{ $editReq->reviewer_note }}"</p>
                            @endif
                        </div>
                        <a href="{{ route('supplier-requests.create', $product) }}"
                           class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:border-farm-400 hover:bg-farm-50 hover:text-farm-700 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Request Edit Again
                        </a>
                    @else
                        <a href="{{ route('supplier-requests.create', $product) }}"
                           class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:border-farm-400 hover:bg-farm-50 hover:text-farm-700 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002 2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Request Edit
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
        <div class="rounded-2xl bg-white dark:bg-gray-800 px-6 py-4 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
            {{ $products->links() }}
        </div>
        @endif

        @else
        <div class="rounded-2xl bg-white dark:bg-gray-800 py-20 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60 flex flex-col items-center justify-center text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('No products found for this filter.') }}</p>
            <a href="{{ route('products.index') }}" class="mt-4 btn-secondary text-xs">{{ __('Clear Filter') }}</a>
        </div>
        @endif

    </div>
</x-app-layout>
