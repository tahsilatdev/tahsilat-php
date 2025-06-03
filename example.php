<?php

//require_once 'vendor/autoload.php';
// Or for manual installation:
require_once 'init.php';

// Initialize client
$tahsilat = new \Tahsilat\TahsilatClient('sk_test_YOUR_API_KEY');

// Create a customer
$customer = $tahsilat->customers->create([
    'name' => 'Test',
    'lastname' => 'Customer',
    'email' => 'test@example.com',
    'phone' => '5551234567',
    'metadata' => [
        'source' => 'web'
    ]
]);

echo "Customer created: " . $customer->id . PHP_EOL;