<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DataRestorationRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataRestorationRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $manager = User::where('role', 'manager')->first();

        if (!$manager && !$admin) {
            $this->command->warn('Skipping DataRestorationRequestSeeder: no admin/manager users found.');
            return;
        }

        // Soft-delete a few products and categories so restoration requests make sense
        $productToDelete1 = Product::where('name', 'Watering Can')->first();
        $productToDelete2 = Product::where('name', 'Rice Seeds')->first();
        $categoryToDelete = Category::where('name', 'Tools')->first();

        $deletedProducts = collect();
        $deletedCategories = collect();

        if ($productToDelete1) {
            $productToDelete1->delete();
            $deletedProducts->push($productToDelete1);
        }
        if ($productToDelete2) {
            $productToDelete2->delete();
            $deletedProducts->push($productToDelete2);
        }
        if ($categoryToDelete) {
            $categoryToDelete->delete();
            $deletedCategories->push($categoryToDelete);
        }

        $requester = $manager ?? $admin;

        $requests = [];

        // Approved restoration request for a product
        if ($deletedProducts->isNotEmpty()) {
            $requests[] = [
                'user_id'    => $requester->id,
                'model_type' => 'App\\Models\\Product',
                'model_id'   => $deletedProducts->first()->id,
                'reason'     => 'This product was accidentally deleted. Customers are still ordering watering cans and we need the inventory record restored.',
                'status'     => 'approved',
                'admin_id'   => $admin?->id,
                'created_at' => now()->subDays(5),
            ];
        }

        // Pending restoration request for a product
        if ($deletedProducts->count() > 1) {
            $requests[] = [
                'user_id'    => $requester->id,
                'model_type' => 'App\\Models\\Product',
                'model_id'   => $deletedProducts->last()->id,
                'reason'     => 'Rice Seeds were removed by mistake during inventory cleanup. Please restore the record so we can resume tracking stock levels.',
                'status'     => 'pending',
                'admin_id'   => null,
                'created_at' => now()->subDay(),
            ];
        }

        // Rejected restoration request for a category
        if ($deletedCategories->isNotEmpty()) {
            $requests[] = [
                'user_id'    => $requester->id,
                'model_type' => 'App\\Models\\Category',
                'model_id'   => $deletedCategories->first()->id,
                'reason'     => 'The Tools category was archived prematurely. Several products still reference this category.',
                'status'     => 'rejected',
                'admin_id'   => $admin?->id,
                'created_at' => now()->subDays(3),
            ];
        }

        // Additional pending request
        if ($deletedCategories->isNotEmpty()) {
            $requests[] = [
                'user_id'    => $requester->id,
                'model_type' => 'App\\Models\\Category',
                'model_id'   => $deletedCategories->first()->id,
                'reason'     => 'Re-submitting restoration request for Tools category. New products from FarmTool Depot need this category.',
                'status'     => 'pending',
                'admin_id'   => null,
                'created_at' => now()->subHours(12),
            ];
        }

        foreach ($requests as $data) {
            DataRestorationRequest::create($data);
        }
    }
}
