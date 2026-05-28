<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Edit Product') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Update product details and stock thresholds.') }}</p>
        </div>
    </x-slot>

    <div class="animate-fade-in">
        <div class="mx-auto max-w-2xl">
            <div class="rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-sm dark:shadow-gray-900/50 ring-1 ring-gray-200/60 dark:ring-gray-700/60">
                <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full rounded-xl" :value="old('name', $product->name)" placeholder="Example: Hybrid Tomato Seeds" required autofocus />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Enter the exact product name used in inventory.</p>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500" required>
                                <option value="">{{ __('Select Category - choose the product group') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Choose the category that best matches the product.</p>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="supplier_id" :value="__('Supplier')" />
                            <select id="supplier_id" name="supplier_id" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500">
                                <option value="">{{ __('Select Supplier / Company (Optional)') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Choose the company linked to this product.</p>
                            <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="reorder_level" :value="__('Reorder Level')" />
                            <x-text-input id="reorder_level" name="reorder_level" type="number" class="mt-1 block w-full rounded-xl" :value="old('reorder_level', $product->reorder_level)" min="0" placeholder="Example: 10" required />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Use the point where you want to be warned to restock.</p>
                            <x-input-error :messages="$errors->get('reorder_level')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="price" :value="__('Selling Price')" />
                            <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full rounded-xl" :value="old('price', $product->price)" min="0" placeholder="Example: 125.00" required />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Enter the price customers will pay.</p>
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="cost_price" :value="__('Cost Price')" />
                            <x-text-input id="cost_price" name="cost_price" type="number" step="0.01" class="mt-1 block w-full rounded-xl" :value="old('cost_price', $product->cost_price)" min="0" placeholder="Example: 90.00" required />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Enter the amount paid to buy or produce this item.</p>
                            <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description">
                            {{ __('Description') }} <span class="text-xs text-gray-500 dark:text-gray-400 font-normal ml-1">({{ __('Optional') }})</span>
                        </x-input-label>
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm dark:shadow-gray-900/50 focus:border-farm-500 focus:ring-farm-500" placeholder="Example: Premium organic corn seeds, 1 kg pack, suitable for planting in dry season">{{ old('description', $product->description) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">Add a short description to help staff identify the product quickly.</p>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <x-primary-button>{{ __('Update Product') }}</x-primary-button>
                        <a href="{{ route('products.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>