<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name'           => 'AgriCorp Fertilizers',
                'email'          => 'contact@agricorp.ph',
                'phone'          => '+639171234001',
                'contact_person' => 'Mang Kiko',
                'city'           => 'Quezon City',
                'province'       => 'Metro Manila',
                'postal_code'    => '1100',
                'street_address' => '12 Fertilizer Ave',
                'account' => [
                    'name'     => 'AgriCorp Account',
                    'email'    => 'supplier.agricorp@farm.com',
                    'password' => 'supplier123',
                ],
            ],
            [
                'name'           => 'GreenLeaf Seeds Co.',
                'email'          => 'info@greenleafseeds.ph',
                'phone'          => '+639171234002',
                'contact_person' => 'Aling Nena',
                'city'           => 'Cabanatuan',
                'province'       => 'Nueva Ecija',
                'postal_code'    => '3100',
                'street_address' => '45 Seedling Street',
                'account' => [
                    'name'     => 'GreenLeaf Account',
                    'email'    => 'supplier.greenleaf@farm.com',
                    'password' => 'supplier123',
                ],
            ],
            [
                'name'           => 'PestShield Solutions',
                'email'          => 'sales@pestshield.ph',
                'phone'          => '+639171234003',
                'contact_person' => 'Kuya Ben',
                'city'           => 'Davao City',
                'province'       => 'Davao del Sur',
                'postal_code'    => '8000',
                'street_address' => '78 Chemical Row',
                'account' => [
                    'name'     => 'PestShield Account',
                    'email'    => 'supplier.pestshield@farm.com',
                    'password' => 'supplier123',
                ],
            ],
            [
                'name'           => 'LiveStock Pro Feeds',
                'email'          => 'order@livestockpro.ph',
                'phone'          => '+639171234004',
                'contact_person' => 'Ate Linda',
                'city'           => 'Pampanga',
                'province'       => 'Central Luzon',
                'postal_code'    => '2000',
                'street_address' => '33 Poultry Lane',
                'account' => [
                    'name'     => 'LiveStock Pro Account',
                    'email'    => 'supplier.livestock@farm.com',
                    'password' => 'supplier123',
                ],
            ],
            [
                'name'           => 'FarmTool Depot',
                'email'          => 'support@farmtooldepot.ph',
                'phone'          => '+639171234005',
                'contact_person' => 'Manong Romy',
                'city'           => 'Cebu City',
                'province'       => 'Cebu',
                'postal_code'    => '6000',
                'street_address' => '9 Hardware Blvd',
                'account' => [
                    'name'     => 'FarmTool Depot Account',
                    'email'    => 'supplier.farmtool@farm.com',
                    'password' => 'supplier123',
                ],
            ],
        ];

        foreach ($suppliers as $data) {
            $accountData = $data['account'];
            unset($data['account']);

            $supplier = Supplier::create(array_merge($data, [
                'is_active'      => true,
                'is_blacklisted' => false,
            ]));

            // Create a bound supplier user account
            User::create([
                'name'             => $accountData['name'],
                'email'            => $accountData['email'],
                'password'         => Hash::make($accountData['password']),
                'plain_password'   => $accountData['password'],
                'role'             => 'supplier',
                'supplier_id'      => $supplier->id,
                'email_verified_at' => now(),
            ]);
        }
    }
}