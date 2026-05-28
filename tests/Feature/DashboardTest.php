<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\DataRestorationRequest;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin dashboard shows admin metrics and hides inventory metrics and stock requests navigation', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some dummy records so the dashboard queries don't fail and return positive counts
    User::factory()->count(2)->create(['role' => 'manager']);
    User::factory()->create(['role' => 'manager', 'is_restricted' => true]);
    DataRestorationRequest::create([
        'user_id' => $admin->id,
        'model_type' => Product::class,
        'model_id' => 999,
        'reason' => 'accidental delete',
        'status' => 'pending'
    ]);
    AuditLog::create([
        'user_id' => $admin->id,
        'action' => 'delete',
        'auditable_type' => Product::class,
        'auditable_id' => 999,
        'ip_address' => '127.0.0.1'
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();

    // Assert admin metrics are displayed
    $response->assertSee('Manager Accounts');
    $response->assertSee('Restricted Accounts');
    $response->assertSee('Pending Restorations');
    $response->assertSee('System Audit Logs');
    $response->assertSee('System Activity (Audit Logs)');
    $response->assertSee('Restorations Status');
    $response->assertSee('Recent System Activities');

    // Assert operational inventory components are NOT displayed
    $response->assertDontSee('Stock Breakdown');
    $response->assertDontSee('Stock by Category');
    $response->assertDontSee('Stock Movement Trends');

    // Assert "Stock Requests" and "Trash" navigation links are NOT displayed for admin
    $response->assertDontSee('Stock Requests');
    $response->assertDontSee('Trash');
});

test('manager dashboard shows operational metrics and shows stock requests navigation', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    // Create some operational data
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
    $product = Product::create([
        'category_id' => $category->id,
        'supplier_id' => $supplier->id,
        'name' => 'Tomato Seeds',
        'description' => 'Tomato seeds',
        'price' => 10.0,
        'cost_price' => 8.0,
        'stock_quantity' => 20,
        'reorder_level' => 5,
        'unit' => 'packs'
    ]);

    $response = $this->actingAs($manager)->get(route('dashboard'));

    $response->assertOk();

    // Assert operational metrics are displayed
    $response->assertSee('Categories');
    $response->assertSee('Products');
    $response->assertSee('Attention Needed');
    $response->assertSee('Suppliers');
    $response->assertSee('Stock Breakdown');
    $response->assertSee('Stock by Category');
    $response->assertSee('Stock Movement Trends');

    // Assert admin metrics are NOT displayed
    $response->assertDontSee('Manager Accounts');
    $response->assertDontSee('Restricted Accounts');
    $response->assertDontSee('Pending Restorations');
    $response->assertDontSee('System Activity (Audit Logs)');
    $response->assertDontSee('Restorations Status');
    $response->assertDontSee('Recent System Activities');

    // Assert "Stock Requests" and "Trash" navigation links are displayed for manager
    $response->assertSee('Stock Requests');
    $response->assertSee('Trash');
});
