<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\SupplierProfileEditRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierProfileEditRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $suppliers = Supplier::all();

        if ($suppliers->isEmpty()) {
            $this->command->warn('Skipping SupplierProfileEditRequestSeeder: no suppliers found.');
            return;
        }

        $requests = [
            // Approved – phone and email update
            [
                'supplier_name'     => 'AgriCorp Fertilizers',
                'requested_changes' => [
                    'phone'          => '+639181234567',
                    'email'          => 'newemail@agricorp.ph',
                    'contact_person' => 'Engr. Kiko Reyes',
                ],
                'status'            => 'approved',
                'rejection_reason'  => null,
                'reviewed_at'       => now()->subDays(4),
                'created_at'        => now()->subDays(6),
            ],
            // Approved – address update
            [
                'supplier_name'     => 'GreenLeaf Seeds Co.',
                'requested_changes' => [
                    'street_address' => '88 New Seedling Road',
                    'barangay'       => 'Brgy. Bagong Silang',
                    'city'           => 'Science City of Muñoz',
                    'province'       => 'Nueva Ecija',
                    'postal_code'    => '3119',
                ],
                'status'            => 'approved',
                'rejection_reason'  => null,
                'reviewed_at'       => now()->subDays(8),
                'created_at'        => now()->subDays(10),
            ],
            // Pending – contact person change
            [
                'supplier_name'     => 'PestShield Solutions',
                'requested_changes' => [
                    'contact_person' => 'Maria Santos',
                    'phone'          => '+639191234888',
                    'email'          => 'maria.santos@pestshield.ph',
                ],
                'status'            => 'pending',
                'rejection_reason'  => null,
                'reviewed_at'       => null,
                'created_at'        => now()->subDays(2),
            ],
            // Pending – address relocation
            [
                'supplier_name'     => 'LiveStock Pro Feeds',
                'requested_changes' => [
                    'street_address' => '150 Agri-Industrial Zone',
                    'city'           => 'San Fernando',
                    'province'       => 'Pampanga',
                    'postal_code'    => '2000',
                ],
                'status'            => 'pending',
                'rejection_reason'  => null,
                'reviewed_at'       => null,
                'created_at'        => now()->subDay(),
            ],
            // Rejected – suspicious changes
            [
                'supplier_name'     => 'FarmTool Depot',
                'requested_changes' => [
                    'name'           => 'FarmTool Depot International',
                    'email'          => 'global@farmtooldepot.com',
                ],
                'status'            => 'rejected',
                'rejection_reason'  => 'Company name changes require supporting business registration documents. Please re-submit with attached proof of name change.',
                'reviewed_at'       => now()->subDays(3),
                'created_at'        => now()->subDays(5),
            ],
        ];

        foreach ($requests as $data) {
            $supplier = $suppliers->firstWhere('name', $data['supplier_name']);

            if (!$supplier) {
                continue;
            }

            // Build original_data from the supplier's current values
            $originalData = [];
            foreach (array_keys($data['requested_changes']) as $field) {
                $originalData[$field] = $supplier->{$field};
            }

            SupplierProfileEditRequest::create([
                'supplier_id'       => $supplier->id,
                'status'            => $data['status'],
                'requested_changes' => $data['requested_changes'],
                'original_data'     => $originalData,
                'reviewed_by_id'    => $data['status'] !== 'pending' ? $admin?->id : null,
                'reviewed_at'       => $data['reviewed_at'],
                'rejection_reason'  => $data['rejection_reason'],
                'created_at'        => $data['created_at'],
                'updated_at'        => $data['reviewed_at'] ?? $data['created_at'],
            ]);
        }
    }
}
