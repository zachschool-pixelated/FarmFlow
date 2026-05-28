<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin/manager users to act as requesters
        $admin = User::where('role', 'admin')->first();
        $manager = User::where('role', 'manager')->first();

        // Get products that have suppliers assigned
        $products = Product::whereNotNull('supplier_id')->with('supplier')->get();

        if ($products->isEmpty() || !$admin) {
            $this->command->warn('Skipping StockRequestSeeder: no products with suppliers or no admin user found.');
            return;
        }

        $requesters = collect([$admin, $manager])->filter();

        $requests = [
            // Completed requests (fulfilled in the past)
            [
                'product_name'       => 'Organic Compost',
                'quantity_requested'  => 12,
                'status'             => 'completed',
                'notes'              => 'Urgent restock needed for upcoming planting season.',
                'shipped_at'         => now()->subDays(12),
                'expected_delivery_at' => now()->subDays(10),
                'created_at'         => now()->subDays(15),
            ],
            [
                'product_name'       => 'Ampalaya Seeds',
                'quantity_requested'  => 16,
                'status'             => 'completed',
                'notes'              => 'Customer demand increasing for bitter melon seeds.',
                'shipped_at'         => now()->subDays(8),
                'expected_delivery_at' => now()->subDays(6),
                'created_at'         => now()->subDays(10),
            ],
            [
                'product_name'       => 'Herbicide Spray',
                'quantity_requested'  => 10,
                'status'             => 'completed',
                'notes'              => 'Standard restock order.',
                'shipped_at'         => now()->subDays(20),
                'expected_delivery_at' => now()->subDays(18),
                'created_at'         => now()->subDays(25),
            ],

            // Shipped requests (in transit)
            [
                'product_name'       => 'Hog Grower Feed',
                'quantity_requested'  => 12,
                'status'             => 'shipped',
                'notes'              => 'Please prioritize delivery, stock running critically low.',
                'shipped_at'         => now()->subDays(2),
                'expected_delivery_at' => now()->addDays(3),
                'created_at'         => now()->subDays(5),
            ],
            [
                'product_name'       => 'Hand Sprayer',
                'quantity_requested'  => 8,
                'status'             => 'shipped',
                'notes'              => 'Restock for rainy season demand.',
                'shipped_at'         => now()->subDay(),
                'expected_delivery_at' => now()->addDays(4),
                'created_at'         => now()->subDays(4),
            ],

            // Processing requests (acknowledged by supplier)
            [
                'product_name'       => 'Urea Fertilizer',
                'quantity_requested'  => 16,
                'status'             => 'processing',
                'notes'              => 'Bulk order for wholesale client.',
                'expected_delivery_at' => now()->addDays(7),
                'created_at'         => now()->subDays(3),
            ],

            // Pending requests (awaiting supplier acknowledgment)
            [
                'product_name'       => 'Fungicide Mix',
                'quantity_requested'  => 12,
                'status'             => 'pending',
                'notes'              => 'Preventive restock before wet season.',
                'created_at'         => now()->subDay(),
            ],
            [
                'product_name'       => 'Layer Feed',
                'quantity_requested'  => 16,
                'status'             => 'pending',
                'notes'              => 'Regular monthly restocking.',
                'created_at'         => now()->subHours(6),
            ],

            // Rejected request
            [
                'product_name'       => 'Garden Hoe',
                'quantity_requested'  => 10,
                'status'             => 'rejected',
                'notes'              => 'Need more units for new farm clients.',
                'created_at'         => now()->subDays(7),
            ],
        ];

        foreach ($requests as $index => $data) {
            $product = $products->firstWhere('name', $data['product_name']);

            if (!$product) {
                continue;
            }

            // Alternate between admin and manager as requester
            $requester = $requesters[$index % $requesters->count()];

            StockRequest::create([
                'product_id'          => $product->id,
                'supplier_id'         => $product->supplier_id,
                'user_id'             => $requester->id,
                'quantity_requested'   => $data['quantity_requested'],
                'status'              => $data['status'],
                'notes'               => $data['notes'],
                'shipped_at'          => $data['shipped_at'] ?? null,
                'expected_delivery_at' => $data['expected_delivery_at'] ?? null,
                'created_at'          => $data['created_at'],
                'updated_at'          => $data['created_at'],
            ]);
        }
    }
}
