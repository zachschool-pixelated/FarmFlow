<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => Category::withCount('products')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('categories.index')->with('status', 'Category created successfully.');
    }

    public function show(Category $category): View
    {
        $category->load(['products', 'supplier']);
        $suppliers = \App\Models\Supplier::where('is_active', true)->where('is_blacklisted', false)->get();
        return view('categories.show', compact('category', 'suppliers'));
    }

    public function update(\Illuminate\Http\Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.show', $category)->with('status', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete category because it contains products.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('status', 'Category deleted successfully.');
    }
}