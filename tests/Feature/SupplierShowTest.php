<?php

use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockRequest;

test('unauthenticated users are redirected from supplier show page', function () {
    $supplier = Supplier::create([
        'name' => 'Supplier Test',
        'email' => 'supplier@test.com',
    ]);
    
    $response = $this->get(route('suppliers.show', $supplier));
    $response->assertRedirect(route('login'));
});

test('managers can access supplier show page and see details, but admins are forbidden', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $manager = User::factory()->create(['role' => 'manager']);
    
    $supplier = Supplier::create([
        'name' => 'Harvest Co',
        'email' => 'harvest@co.test',
        'phone' => '09170001111',
    ]);

    $category = Category::create([
        'name' => 'Organic Fertilizers',
        'description' => 'Soil health products',
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'name' => 'Organic Compost Pack',
        'description' => 'Compost pack',
        'price' => 200.00,
        'cost_price' => 150.00,
        'stock_quantity' => 12,
        'reorder_level' => 5,
        'unit' => 'bags',
    ]);

    // Create a stock request to verify transactions and last delivery details
    $stockRequest = StockRequest::create([
        'product_id' => $product->id,
        'supplier_id' => $supplier->id,
        'user_id' => $manager->id,
        'quantity_requested' => 100,
        'status' => 'completed',
        'shipped_at' => now()->subDay(),
    ]);

    // 1. Manager should see the show details successfully
    $response = $this->actingAs($manager)->get(route('suppliers.show', $supplier));
    $response->assertOk();
    $response->assertSee('Harvest Co');
    $response->assertSee('Organic Compost Pack');
    $response->assertSee('100 units of Organic Compost Pack');
    $response->assertSee('Units Received');
    $response->assertSee('Products Offered');

    // 2. Admin should get a 403 Forbidden status
    $response = $this->actingAs($admin)->get(route('suppliers.show', $supplier));
    $response->assertStatus(403);
});
