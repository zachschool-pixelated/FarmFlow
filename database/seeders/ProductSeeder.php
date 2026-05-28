<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::with('supplier')->get()->keyBy('name');

        $products = [
            // Fertilizers → AgriCorp Fertilizers
            ['category' => 'Fertilizers', 'name' => 'Urea Fertilizer',      'description' => '46-0-0 nitrogen fertilizer',       'unit' => 'sack',  'price' => 1250.00, 'cost_price' => 1100.00, 'stock_quantity' => 20, 'reorder_level' => 8],
            ['category' => 'Fertilizers', 'name' => 'Complete Fertilizer',   'description' => 'Balanced nutrient mix',             'unit' => 'sack',  'price' => 1350.00, 'cost_price' => 1180.00, 'stock_quantity' => 18, 'reorder_level' => 8],
            ['category' => 'Fertilizers', 'name' => 'Organic Compost',       'description' => 'Rich organic compost blend',        'unit' => 'sack',  'price' =>  850.00, 'cost_price' =>  700.00, 'stock_quantity' =>  5, 'reorder_level' => 6],

            // Seeds → GreenLeaf Seeds Co.
            ['category' => 'Seeds',       'name' => 'Hybrid Corn Seeds',     'description' => 'Yellow hybrid corn',               'unit' => 'kg',    'price' =>  320.00, 'cost_price' =>  250.00, 'stock_quantity' => 30, 'reorder_level' => 10],
            ['category' => 'Seeds',       'name' => 'Rice Seeds',            'description' => 'Certified rice seed',              'unit' => 'kg',    'price' =>  280.00, 'cost_price' =>  220.00, 'stock_quantity' => 25, 'reorder_level' => 10],
            ['category' => 'Seeds',       'name' => 'Ampalaya Seeds',        'description' => 'Bitter melon variety seeds',       'unit' => 'pack',  'price' =>   95.00, 'cost_price' =>   60.00, 'stock_quantity' =>  4, 'reorder_level' =>  8],

            // Pesticides → PestShield Solutions
            ['category' => 'Pesticides',  'name' => 'Insect Spray',          'description' => 'General pest control',             'unit' => 'liter', 'price' =>  450.00, 'cost_price' =>  380.00, 'stock_quantity' => 12, 'reorder_level' => 6],
            ['category' => 'Pesticides',  'name' => 'Fungicide Mix',         'description' => 'Disease prevention formula',       'unit' => 'liter', 'price' =>  520.00, 'cost_price' =>  430.00, 'stock_quantity' => 10, 'reorder_level' => 6],
            ['category' => 'Pesticides',  'name' => 'Herbicide Spray',       'description' => 'Broad-spectrum weed killer',       'unit' => 'liter', 'price' =>  480.00, 'cost_price' =>  390.00, 'stock_quantity' =>  3, 'reorder_level' => 5],

            // Animal Feeds → LiveStock Pro Feeds
            ['category' => 'Animal Feeds','name' => 'Broiler Feed',          'description' => 'Starter feed for broilers',        'unit' => 'sack',  'price' =>  980.00, 'cost_price' =>  840.00, 'stock_quantity' => 22, 'reorder_level' => 8],
            ['category' => 'Animal Feeds','name' => 'Layer Feed',            'description' => 'Feed for egg layers',              'unit' => 'sack',  'price' => 1020.00, 'cost_price' =>  875.00, 'stock_quantity' => 16, 'reorder_level' => 8],
            ['category' => 'Animal Feeds','name' => 'Hog Grower Feed',       'description' => 'Growing stage hog ration',         'unit' => 'sack',  'price' =>  960.00, 'cost_price' =>  800.00, 'stock_quantity' =>  4, 'reorder_level' => 6],

            // Tools → FarmTool Depot
            ['category' => 'Tools',       'name' => 'Garden Hoe',            'description' => 'Hand tool for soil work',          'unit' => 'piece', 'price' =>  210.00, 'cost_price' =>  160.00, 'stock_quantity' => 15, 'reorder_level' => 5],
            ['category' => 'Tools',       'name' => 'Watering Can',          'description' => 'Plastic watering can',             'unit' => 'piece', 'price' =>  180.00, 'cost_price' =>  130.00, 'stock_quantity' => 14, 'reorder_level' => 5],
            ['category' => 'Tools',       'name' => 'Hand Sprayer',          'description' => 'Manual pump garden sprayer',       'unit' => 'piece', 'price' =>  350.00, 'cost_price' =>  270.00, 'stock_quantity' =>  2, 'reorder_level' => 4],
        ];

        foreach ($products as $product) {
            $category = $categories[$product['category']];

            Product::create([
                'category_id'    => $category->id,
                'supplier_id'    => $category->supplier_id,
                'name'           => $product['name'],
                'description'    => $product['description'],
                'unit'           => $product['unit'],
                'price'          => $product['price'],
                'cost_price'     => $product['cost_price'],
                'stock_quantity' => $product['stock_quantity'],
                'reorder_level'  => $product['reorder_level'],
            ]);
        }
    }
}