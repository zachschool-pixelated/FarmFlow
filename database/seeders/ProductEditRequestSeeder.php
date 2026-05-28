<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductEditRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductEditRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Get supplier users
        $supplierUsers = User::where('role', 'supplier')->whereNotNull('supplier_id')->get();

        if ($supplierUsers->isEmpty()) {
            $this->command->warn('Skipping ProductEditRequestSeeder: no supplier users found.');
            return;
        }

        $products = Product::whereNotNull('supplier_id')->with('supplier')->get();

        if ($products->isEmpty()) {
            $this->command->warn('Skipping ProductEditRequestSeeder: no products with suppliers found.');
            return;
        }

        $requests = [
            // Approved request – price update
            [
                'product_name'      => 'Urea Fertilizer',
                'reason'            => 'Raw material costs have increased. Requesting price adjustment to reflect current market value.',
                'requested_changes' => [
                    'price'      => 1380.00,
                    'cost_price' => 1200.00,
                ],
                'status'            => 'approved',
                'reviewer_note'     => 'Approved. Price adjusted per supplier cost sheet dated May 2026.',
                'reviewed_at'       => now()->subDays(3),
                'created_at'        => now()->subDays(5),
            ],
            // Approved request – description update
            [
                'product_name'      => 'Hybrid Corn Seeds',
                'reason'            => 'Updated product description to include new hybrid variant specifications.',
                'requested_changes' => [
                    'description' => 'Premium yellow hybrid corn seeds, drought-resistant variety, suitable for lowland and upland planting.',
                ],
                'status'            => 'approved',
                'reviewer_note'     => 'Description looks accurate. Approved.',
                'reviewed_at'       => now()->subDays(7),
                'created_at'        => now()->subDays(9),
            ],
            // Pending request – price and description
            [
                'product_name'      => 'Insect Spray',
                'reason'            => 'New formulation released. Updating description and adjusting price for the improved version.',
                'requested_changes' => [
                    'description' => 'Advanced broad-spectrum insect control spray with extended residual protection.',
                    'price'       => 490.00,
                    'cost_price'  => 400.00,
                ],
                'status'            => 'pending',
                'reviewer_note'     => null,
                'reviewed_at'       => null,
                'created_at'        => now()->subDays(2),
            ],
            // Pending request – unit change
            [
                'product_name'      => 'Broiler Feed',
                'reason'            => 'We now package broiler feed in 25kg bags instead of 50kg sacks. Requesting unit label update.',
                'requested_changes' => [
                    'unit' => 'bag',
                ],
                'status'            => 'pending',
                'reviewer_note'     => null,
                'reviewed_at'       => null,
                'created_at'        => now()->subDay(),
            ],
            // Rejected request – price too high
            [
                'product_name'      => 'Complete Fertilizer',
                'reason'            => 'Requesting significant price increase due to import duties.',
                'requested_changes' => [
                    'price'      => 1800.00,
                    'cost_price' => 1600.00,
                ],
                'status'            => 'rejected',
                'reviewer_note'     => 'Price increase of 33% is too steep. Please re-submit with a more reasonable adjustment or provide supporting documentation.',
                'reviewed_at'       => now()->subDays(4),
                'created_at'        => now()->subDays(6),
            ],
            // Rejected request – invalid data
            [
                'product_name'      => 'Garden Hoe',
                'reason'            => 'Correcting product description typo.',
                'requested_changes' => [
                    'description' => 'Premium hand-forged garden hoe with ergonomic hardwood handle for efficient soil cultivation.',
                ],
                'status'            => 'rejected',
                'reviewer_note'     => 'Original description is accurate. No changes needed at this time.',
                'reviewed_at'       => now()->subDays(10),
                'created_at'        => now()->subDays(12),
            ],
        ];

        foreach ($requests as $data) {
            $product = $products->firstWhere('name', $data['product_name']);

            if (!$product) {
                continue;
            }

            // Find the supplier user for this product
            $supplierUser = $supplierUsers->firstWhere('supplier_id', $product->supplier_id);

            if (!$supplierUser) {
                continue;
            }

            // Build original values from the product's current data
            $originalValues = [];
            foreach (array_keys($data['requested_changes']) as $field) {
                $originalValues[$field] = $product->{$field};
            }

            ProductEditRequest::create([
                'product_id'        => $product->id,
                'supplier_id'       => $product->supplier_id,
                'user_id'           => $supplierUser->id,
                'status'            => $data['status'],
                'reason'            => $data['reason'],
                'requested_changes' => $data['requested_changes'],
                'original_values'   => $originalValues,
                'reviewer_id'       => $data['status'] !== 'pending' ? $admin?->id : null,
                'reviewer_note'     => $data['reviewer_note'],
                'reviewed_at'       => $data['reviewed_at'],
                'created_at'        => $data['created_at'],
                'updated_at'        => $data['reviewed_at'] ?? $data['created_at'],
            ]);
        }
    }
}
