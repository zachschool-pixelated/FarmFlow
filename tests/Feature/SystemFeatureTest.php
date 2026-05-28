<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockRequest;
use App\Models\ProductEditRequest;
use Illuminate\Support\Facades\Hash;

test('full system features lifecycle', function () {
    // 1. Create admin and manager users
    $admin = User::factory()->create(['role' => 'admin']);
    $manager = User::factory()->create(['role' => 'manager']);

    // 2. Manager creates a category
    $response = $this->actingAs($manager)
        ->post(route('categories.store'), [
            'name' => 'Seeds',
            'description' => 'Various plant seeds',
        ]);
    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', ['name' => 'Seeds']);
    $category = Category::where('name', 'Seeds')->first();

    // 3. Manager creates a supplier and linked supplier user account
    $response = $this->actingAs($manager)
        ->post(route('suppliers.store'), [
            'name' => 'Green Harvest Supplies',
            'email' => 'orders@greenharvest.test',
            'phone' => '09171234567',
            'street_address' => 'San Pablo',
            'city' => 'San Pablo City',
            'province' => 'Laguna',
            'postal_code' => '4000',
            'contacts' => [
                [
                    'name' => 'Maria Santos',
                    'role' => 'Sales Representative',
                    'phone' => '09171112222',
                    'email' => 'maria@greenharvest.test',
                    'notes' => 'Primary contact',
                    'is_primary' => true,
                ]
            ],
            'create_account' => '1',
            'account_name' => 'Green Harvest Manager',
            'account_email' => 'supplier@greenharvest.test',
            'account_password' => 'password123',
        ]);
    $response->assertRedirect(route('suppliers.index'));
    
    $this->assertDatabaseHas('suppliers', ['name' => 'Green Harvest Supplies']);
    $supplier = Supplier::where('name', 'Green Harvest Supplies')->first();
    
    $this->assertDatabaseHas('users', [
        'email' => 'supplier@greenharvest.test',
        'role' => 'supplier',
        'supplier_id' => $supplier->id,
    ]);
    $supplierUser = User::where('email', 'supplier@greenharvest.test')->first();

    // 4. Access supplier dashboard as supplier user
    $response = $this->actingAs($supplierUser)
        ->get(route('suppliers.dashboard', $supplier));
    $response->assertOk();

    // 5. Manager assigns the supplier to the category
    $response = $this->actingAs($manager)
        ->put(route('categories.update', $category), [
            'supplier_id' => $supplier->id,
        ]);
    $response->assertRedirect(route('categories.show', $category));
    $category->refresh();
    $this->assertEquals($supplier->id, $category->supplier_id);

    // 6. Manager creates a product under the category and links it to the supplier
    $response = $this->actingAs($manager)
        ->post(route('products.store'), [
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Tomato Seeds',
            'description' => 'Hybrid tomato seeds pack',
            'price' => 150.00,
            'cost_price' => 100.00,
            'stock_quantity' => 0,
            'reorder_level' => 10,
        ]);
    $product = Product::where('name', 'Tomato Seeds')->first();
    $response->assertRedirect(route('stock-requests.create', $product));
    $this->assertDatabaseHas('products', ['name' => 'Tomato Seeds', 'supplier_id' => $supplier->id]);

    // 7. Manager initiates a stock request for 50 units
    $response = $this->actingAs($manager)
        ->post(route('stock-requests.store'), [
            'product_id' => $product->id,
            'quantity_requested' => 50,
            'notes' => 'Urgent stock needed',
        ]);
    $response->assertRedirect(route('stock-requests.index'));
    $this->assertDatabaseHas('stock_requests', [
        'product_id' => $product->id,
        'quantity_requested' => 50,
        'status' => 'pending',
    ]);
    $stockRequest = StockRequest::where('product_id', $product->id)->first();

    // 8. Supplier user views index and acknowledges the stock request (sets to processing with expected delivery date)
    $response = $this->actingAs($supplierUser)
        ->get(route('stock-requests.index'));
    $response->assertOk();

    $response = $this->actingAs($supplierUser)
        ->put(route('stock-requests.update', $stockRequest), [
            'status' => 'processing',
            'expected_delivery_at' => now()->addDays(3)->toDateString(),
        ]);
    $response->assertRedirect();
    $stockRequest->refresh();
    $this->assertEquals('processing', $stockRequest->status);
    $this->assertNotNull($stockRequest->expected_delivery_at);

    // 9. Supplier user marks request as shipped
    $response = $this->actingAs($supplierUser)
        ->put(route('stock-requests.update', $stockRequest), [
            'status' => 'shipped',
            'expected_delivery_at' => $stockRequest->expected_delivery_at,
        ]);
    $response->assertRedirect();
    $stockRequest->refresh();
    $this->assertEquals('shipped', $stockRequest->status);
    $this->assertNotNull($stockRequest->shipped_at);

    // 10. Manager marks request as completed, checking stock increment and movement logging
    $response = $this->actingAs($manager)
        ->put(route('stock-requests.update', $stockRequest), [
            'status' => 'completed',
        ]);
    $response->assertRedirect();
    $stockRequest->refresh();
    $this->assertEquals('completed', $stockRequest->status);
    
    // Check product stock quantity
    $product->refresh();
    $this->assertEquals(50, $product->stock_quantity);

    // Check stock movement is logged
    $this->assertDatabaseHas('stock_movements', [
        'product_id' => $product->id,
        'type' => 'in',
        'quantity' => 50,
        'stock_before' => 0,
        'stock_after' => 50,
    ]);

    // 11. Supplier user requests a product edit (change name to 'Premium Tomato Seeds')
    $response = $this->actingAs($supplierUser)
        ->post(route('supplier-requests.store', $product), [
            'reason' => 'Updated packaging and branding name',
            'new_name' => 'Premium Tomato Seeds',
            'new_description' => $product->description,
            'new_price' => $product->price,
            'new_unit' => $product->unit,
        ]);
    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('product_edit_requests', [
        'product_id' => $product->id,
        'status' => 'pending',
    ]);
    $editRequest = ProductEditRequest::where('product_id', $product->id)->first();

    // 12. Admin attempts to review but gets Forbidden
    $response = $this->actingAs($admin)
        ->get(route('supplier-requests.show', $editRequest));
    $response->assertStatus(403);

    $response = $this->actingAs($admin)
        ->put(route('supplier-requests.update', $editRequest), [
            'action' => 'approve',
            'reviewer_note' => 'Looks good',
        ]);
    $response->assertStatus(403);

    // 13. Manager reviews and approves the product edit request
    $response = $this->actingAs($manager)
        ->get(route('supplier-requests.show', $editRequest));
    $response->assertOk();

    $response = $this->actingAs($manager)
        ->put(route('supplier-requests.update', $editRequest), [
            'action' => 'approve',
            'reviewer_note' => 'Looks good',
        ]);
    $response->assertRedirect(route('supplier-requests.index'));
    $product->refresh();
    $this->assertEquals('Premium Tomato Seeds', $product->name);
    $editRequest->refresh();
    $this->assertEquals('approved', $editRequest->status);
});
