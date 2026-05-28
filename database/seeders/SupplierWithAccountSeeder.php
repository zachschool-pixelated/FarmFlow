<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SupplierWithAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // 1. Create a dummy supplier
        $supplier = Supplier::create([
            'name' => 'AgriCorp Supplies',
            'email' => 'contact@agricorp.com',
            'phone' => '+639123456789',
            'supplier_code' => 'SUP-AGRI-' . strtoupper(Str::random(4)),
            'is_active' => true,
            'is_blacklisted' => false,
            'address' => '123 Farmville Road, Metro Manila, NCR, 1000, Philippines',
            'city' => 'Metro Manila',
            'province' => 'NCR',
            'postal_code' => '1000',
        ]);

        // 2. Create the bound supplier user account
        User::create([
            'name' => 'Juan Dela Cruz (AgriCorp)',
            'email' => 'juan.agricorp@example.com',
            'password' => Hash::make('password123'),
            'plain_password' => 'password123',
            'role' => 'supplier',
            'supplier_id' => $supplier->id,
            'email_verified_at' => now(),
        ]);
        
        $this->command->info('Successfully seeded AgriCorp Supplies and its user account (juan.agricorp@example.com / password123)');
    }
}
