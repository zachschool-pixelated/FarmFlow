<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockRequest;

test('unauthenticated users are redirected to login from notifications page', function () {
    $response = $this->get(route('notifications.index'));
    $response->assertRedirect(route('login'));
});

test('manager can access notifications page and see active notifications', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    
    // Create a product with low stock to trigger notification
    $category = Category::create([
        'name' => 'Tomato Seeds Category',
        'description' => 'Seeds desc',
    ]);
    $supplier = Supplier::create([
        'name' => 'Supplier Inc',
        'email' => 'supplier@test.com',
        'phone' => '09172223333',
        'street_address' => 'Street',
        'city' => 'City',
        'province' => 'Province',
        'postal_code' => '1234',
        'contacts' => [],
    ]);
    $product = Product::create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'name' => 'Tomato Seeds Test Alert',
        'description' => 'Tomato Seeds pack',
        'price' => 150.00,
        'cost_price' => 100.00,
        'stock_quantity' => 0,
        'reorder_level' => 10,
        'unit' => 'packs',
    ]);

    $response = $this->actingAs($manager)->get(route('notifications.index'));
    $response->assertOk();
    $response->assertSee('Tomato Seeds Test Alert');
    $response->assertSee('Out of stock');
});

test('admins do not see product and profile edit requests in notifications or navigation bar, but managers do', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $manager = User::factory()->create(['role' => 'manager']);
    
    $category = Category::create([
        'name' => 'Seeds Category',
        'description' => 'Seeds desc',
    ]);
    
    $supplier = Supplier::create([
        'name' => 'Supplier Co',
        'email' => 'supplier.co@test.com',
        'phone' => '09172223334',
        'street_address' => 'Street',
        'city' => 'City',
        'province' => 'Province',
        'postal_code' => '1234',
        'contacts' => [],
    ]);
    
    $supplierUser = User::factory()->create([
        'role' => 'supplier',
        'supplier_id' => $supplier->id,
        'email' => 'supplier.co.user@test.com'
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'name' => 'Standard Fertilizer',
        'description' => 'Compost',
        'price' => 150.00,
        'cost_price' => 100.00,
        'stock_quantity' => 100,
        'reorder_level' => 10,
        'unit' => 'packs',
    ]);

    // Create a pending product edit request
    $productEdit = \App\Models\ProductEditRequest::create([
        'product_id' => $product->id,
        'supplier_id' => $supplier->id,
        'user_id' => $supplierUser->id,
        'status' => 'pending',
        'reason' => 'Need price update',
        'requested_changes' => ['price' => 200.00],
        'original_values' => ['price' => 150.00],
    ]);

    // Create a pending profile edit request
    $profileEdit = \App\Models\SupplierProfileEditRequest::create([
        'supplier_id' => $supplier->id,
        'status' => 'pending',
        'requested_changes' => ['phone' => '09178889999'],
        'original_data' => ['phone' => '09172223334'],
    ]);

    // 1. Admin view check: Notifications page should NOT contain the edit requests
    $response = $this->actingAs($admin)->get(route('notifications.index'));
    $response->assertOk();
    $response->assertDontSee('Product Edit: Standard Fertilizer');
    $response->assertDontSee('Profile Edit: Supplier Co');

    // 2. Admin view check: Layout (e.g. Dashboard) should NOT render these notification items
    $response = $this->actingAs($admin)->get(route('dashboard'));
    $response->assertOk();
    $response->assertDontSee('Product Edit: Standard Fertilizer');
    $response->assertDontSee('Profile Edit: Supplier Co');
    // Also, admin navigation sidebar should NOT have Product/Profile Edit Requests links
    $response->assertDontSee('Product Edit Requests');
    $response->assertDontSee('Profile Edit Requests');

    // 3. Admin direct URL access check: Admin should receive 403 when trying to access directly
    $response = $this->actingAs($admin)->get(route('supplier-requests.index'));
    $response->assertStatus(403);
    
    $response = $this->actingAs($admin)->get(route('supplier-profile-requests.index'));
    $response->assertStatus(403);

    // 4. Manager view check: Notifications page should contain the edit requests
    $response = $this->actingAs($manager)->get(route('notifications.index'));
    $response->assertOk();
    $response->assertSee('Product Edit: Standard Fertilizer');
    $response->assertSee('Profile Edit: Supplier Co');

    // 5. Manager view check: Layout should render these notification items
    $response = $this->actingAs($manager)->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee('Product Edit: Standard Fertilizer');
    $response->assertSee('Profile Edit: Supplier Co');
    $response->assertSee('Product Edit Requests');
    $response->assertSee('Profile Edit Requests');

    // 6. Manager direct URL access check: Manager should receive 200/Ok
    $response = $this->actingAs($manager)->get(route('supplier-requests.index'));
    $response->assertOk();
    
    $response = $this->actingAs($manager)->get(route('supplier-profile-requests.index'));
    $response->assertOk();
});

test('role-based access boundaries for admin and manager resources', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $manager = User::factory()->create(['role' => 'manager']);
    $supplierUser = User::factory()->create(['role' => 'supplier']);

    // Admin Access Boundaries
    $this->actingAs($admin);
    // Blocked
    $this->get(route('categories.index'))->assertStatus(403);
    $this->get(route('products.index'))->assertStatus(403);
    $this->get(route('suppliers.index'))->assertStatus(403);
    $this->get(route('stock-movements.index'))->assertStatus(403);
    $this->get(route('stock-requests.index'))->assertStatus(403);
    // Allowed
    $this->get(route('reports.index'))->assertOk();
    $this->get(route('users.index'))->assertOk();
    $this->get(route('trash.index'))->assertOk();
    $this->get(route('data-restorations.index'))->assertOk();
    $this->get(route('audit-logs.index'))->assertOk();

    // Manager Access Boundaries
    $this->actingAs($manager);
    // Allowed
    $this->get(route('categories.index'))->assertOk();
    $this->get(route('products.index'))->assertOk();
    $this->get(route('suppliers.index'))->assertOk();
    $this->get(route('stock-movements.index'))->assertOk();
    $this->get(route('stock-requests.index'))->assertOk();
    $this->get(route('reports.index'))->assertOk();
    $this->get(route('users.index'))->assertOk();
    $this->get(route('trash.index'))->assertOk();
    // Blocked
    $this->get(route('data-restorations.index'))->assertStatus(403);
    $this->get(route('audit-logs.index'))->assertStatus(403);

    // UserController Boundaries:
    // Admin can edit/restrict manager but not supplier
    $this->actingAs($admin);
    $this->get(route('users.edit', $manager))->assertOk();
    $this->get(route('users.edit', $supplierUser))->assertStatus(403);

    // Manager can edit/restrict supplier but not admin or manager
    $this->actingAs($manager);
    $this->get(route('users.edit', $supplierUser))->assertOk();
    $this->get(route('users.edit', $manager))->assertStatus(403);
});

