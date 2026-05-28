<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,                        // Admin + Manager accounts
            SupplierSeeder::class,                    // 5 suppliers, each with a bound user account
            CategorySeeder::class,                    // 5 categories, each bound to a supplier
            ProductSeeder::class,                     // Products bound to category + supplier
            StockRequestSeeder::class,                // Stock requests across all statuses
            ProductEditRequestSeeder::class,          // Product edit requests from suppliers
            SupplierProfileEditRequestSeeder::class,  // Supplier profile change requests
            DataRestorationRequestSeeder::class,      // Data restoration requests (soft-deletes items first)
            AuditLogSeeder::class,                    // System audit log history
        ]);
    }
}

