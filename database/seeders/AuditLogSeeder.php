<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $manager = User::where('role', 'manager')->first();
        $supplierUsers = User::where('role', 'supplier')->get();

        if (!$admin) {
            $this->command->warn('Skipping AuditLogSeeder: no admin user found.');
            return;
        }

        $allUsers = collect([$admin, $manager])->filter()->merge($supplierUsers);

        $products = Product::withTrashed()->get();
        $categories = Category::withTrashed()->get();
        $suppliers = Supplier::all();

        $logs = [];

        // ── Product creation logs ──────────────────────────────────
        foreach ($products->take(5) as $index => $product) {
            $logs[] = [
                'user_id'        => $admin->id,
                'action'         => 'created',
                'auditable_type' => 'App\\Models\\Product',
                'auditable_id'   => $product->id,
                'old_values'     => null,
                'new_values'     => [
                    'name'           => $product->name,
                    'price'          => $product->price,
                    'cost_price'     => $product->cost_price,
                    'stock_quantity' => $product->stock_quantity,
                    'reorder_level'  => $product->reorder_level,
                    'unit'           => $product->unit,
                ],
                'ip_address'     => '127.0.0.1',
                'created_at'     => now()->subDays(30 - $index),
            ];
        }

        // ── Product update logs (price changes, stock adjustments) ─
        foreach ($products->take(3) as $product) {
            $oldPrice = $product->price;
            $newPrice = round($oldPrice * 1.05, 2);

            $logs[] = [
                'user_id'        => $admin->id,
                'action'         => 'updated',
                'auditable_type' => 'App\\Models\\Product',
                'auditable_id'   => $product->id,
                'old_values'     => ['price' => $oldPrice],
                'new_values'     => ['price' => $newPrice],
                'ip_address'     => '127.0.0.1',
                'created_at'     => now()->subDays(15),
            ];
        }

        // ── Stock quantity update logs ─────────────────────────────
        foreach ($products->take(4) as $product) {
            $oldQty = $product->stock_quantity + 10;
            $logs[] = [
                'user_id'        => ($manager ?? $admin)->id,
                'action'         => 'updated',
                'auditable_type' => 'App\\Models\\Product',
                'auditable_id'   => $product->id,
                'old_values'     => ['stock_quantity' => $oldQty],
                'new_values'     => ['stock_quantity' => $product->stock_quantity],
                'ip_address'     => '192.168.1.100',
                'created_at'     => now()->subDays(10),
            ];
        }

        // ── Product deletion logs ──────────────────────────────────
        $trashedProducts = $products->whereNotNull('deleted_at');
        foreach ($trashedProducts as $product) {
            $logs[] = [
                'user_id'        => ($manager ?? $admin)->id,
                'action'         => 'deleted',
                'auditable_type' => 'App\\Models\\Product',
                'auditable_id'   => $product->id,
                'old_values'     => [
                    'name'           => $product->name,
                    'stock_quantity' => $product->stock_quantity,
                ],
                'new_values'     => null,
                'ip_address'     => '192.168.1.100',
                'created_at'     => now()->subDays(5),
            ];
        }

        // ── Category creation logs ────────────────────────────────
        foreach ($categories->take(3) as $index => $category) {
            $logs[] = [
                'user_id'        => $admin->id,
                'action'         => 'created',
                'auditable_type' => 'App\\Models\\Category',
                'auditable_id'   => $category->id,
                'old_values'     => null,
                'new_values'     => ['name' => $category->name],
                'ip_address'     => '127.0.0.1',
                'created_at'     => now()->subDays(28 - $index),
            ];
        }

        // ── Supplier creation logs ────────────────────────────────
        foreach ($suppliers->take(3) as $index => $supplier) {
            $logs[] = [
                'user_id'        => $admin->id,
                'action'         => 'created',
                'auditable_type' => 'App\\Models\\Supplier',
                'auditable_id'   => $supplier->id,
                'old_values'     => null,
                'new_values'     => [
                    'name'           => $supplier->name,
                    'contact_person' => $supplier->contact_person,
                    'email'          => $supplier->email,
                    'city'           => $supplier->city,
                ],
                'ip_address'     => '127.0.0.1',
                'created_at'     => now()->subDays(29 - $index),
            ];
        }

        // ── Supplier update logs ──────────────────────────────────
        if ($suppliers->count() >= 2) {
            $supplier = $suppliers[1];
            $logs[] = [
                'user_id'        => $admin->id,
                'action'         => 'updated',
                'auditable_type' => 'App\\Models\\Supplier',
                'auditable_id'   => $supplier->id,
                'old_values'     => ['phone' => '+639171234002'],
                'new_values'     => ['phone' => $supplier->phone],
                'ip_address'     => '127.0.0.1',
                'created_at'     => now()->subDays(12),
            ];
        }

        // ── User creation logs ────────────────────────────────────
        foreach ($supplierUsers->take(3) as $index => $user) {
            $logs[] = [
                'user_id'        => $admin->id,
                'action'         => 'created',
                'auditable_type' => 'App\\Models\\User',
                'auditable_id'   => $user->id,
                'old_values'     => null,
                'new_values'     => [
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'ip_address'     => '127.0.0.1',
                'created_at'     => now()->subDays(27 - $index),
            ];
        }

        // ── Supplier user login-style update log ──────────────────
        if ($supplierUsers->isNotEmpty()) {
            $logs[] = [
                'user_id'        => $supplierUsers->first()->id,
                'action'         => 'updated',
                'auditable_type' => 'App\\Models\\User',
                'auditable_id'   => $supplierUsers->first()->id,
                'old_values'     => ['name' => 'AgriCorp Account'],
                'new_values'     => ['name' => $supplierUsers->first()->name],
                'ip_address'     => '10.0.0.50',
                'created_at'     => now()->subDays(8),
            ];
        }

        // Sort by created_at for natural ordering
        usort($logs, fn ($a, $b) => $a['created_at'] <=> $b['created_at']);

        foreach ($logs as $log) {
            AuditLog::create([
                'user_id'        => $log['user_id'],
                'action'         => $log['action'],
                'auditable_type' => $log['auditable_type'],
                'auditable_id'   => $log['auditable_id'],
                'old_values'     => $log['old_values'],
                'new_values'     => $log['new_values'],
                'ip_address'     => $log['ip_address'],
                'created_at'     => $log['created_at'],
                'updated_at'     => $log['created_at'],
            ]);
        }
    }
}
