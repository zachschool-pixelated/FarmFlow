<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Supplier names match SupplierSeeder — will be resolved by name after suppliers are seeded
        $categories = [
            ['name' => 'Fertilizers',   'description' => 'Soil nutrition and crop boost products.',    'supplier' => 'AgriCorp Fertilizers'],
            ['name' => 'Seeds',         'description' => 'Vegetable, grain, and specialty seeds.',     'supplier' => 'GreenLeaf Seeds Co.'],
            ['name' => 'Pesticides',    'description' => 'Crop protection and pest control products.', 'supplier' => 'PestShield Solutions'],
            ['name' => 'Animal Feeds',  'description' => 'Feed products for livestock and poultry.',   'supplier' => 'LiveStock Pro Feeds'],
            ['name' => 'Tools',         'description' => 'Basic farm and maintenance tools.',          'supplier' => 'FarmTool Depot'],
        ];

        $supplierMap = Supplier::pluck('id', 'name');

        foreach ($categories as $category) {
            Category::create([
                'name'        => $category['name'],
                'description' => $category['description'],
                'supplier_id' => $supplierMap[$category['supplier']] ?? null,
            ]);
        }
    }
}