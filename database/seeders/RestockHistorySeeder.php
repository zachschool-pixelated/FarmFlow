<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\StockRequest;
use Carbon\Carbon;

class RestockHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'admin']);
        }

        $products = Product::whereNotNull('supplier_id')->get();

        if ($products->isEmpty()) {
            $this->command->info('No products with a supplier found. Please run ProductSeeder first.');
            return;
        }

        $statuses = ['completed'];
        
        foreach ($products->random(min(5, $products->count())) as $product) {
            for ($i = 0; $i < rand(2, 5); $i++) {
                $createdAt = Carbon::now()->subDays(rand(10, 100));
                
                StockRequest::create([
                    'product_id' => $product->id,
                    'supplier_id' => $product->supplier_id,
                    'quantity_requested' => rand(10, 100),
                    'status' => 'completed',
                    'user_id' => $admin->id,
                    'notes' => 'Historical restock request for ' . $product->name,
                    'expected_delivery_at' => $createdAt->copy()->addDays(rand(2, 5)),
                    'shipped_at' => $createdAt->copy()->addDays(1),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(rand(2, 5)),
                ]);
            }
        }

        $this->command->info('Restock history seeded successfully!');
    }
}
