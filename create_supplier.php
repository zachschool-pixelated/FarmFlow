<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// The role enum is already configured in the migrations to support 'supplier', so we do not need to alter the table structure here.

$supplier = App\Models\Supplier::firstOrCreate(
    ['email' => 'orders@greenharvest.test'],
    ['name' => 'Green Harvest Supplies', 'contact_person' => 'Maria Santos', 'phone' => '09171234567', 'address' => 'San Pablo, Laguna']
);

$user = App\Models\User::updateOrCreate(
    ['email' => 'supplier@farmflow.test'],
    ['name' => 'Green Harvest User', 'password' => bcrypt('password123'), 'role' => 'supplier', 'supplier_id' => $supplier->id]
);

echo "Supplier email: " . $user->email . "\nPassword: password123\n";
