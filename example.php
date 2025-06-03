<?php

require_once 'vendor/autoload.php';
// Or for manual installation:
// require_once '/path/to/tahsilat-php/init.php';

// Initialize client
$tahsilat = new \Tahsilat\TahsilatClient('sk_test_YOUR_API_KEY');

try {
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

    // Create a product
    $product = $tahsilat->products->create([
        'product_name' => 'Test Product',
        'price' => 99.99,
        'quantity' => 1,
        'description' => 'This is a test product'
    ]);

    echo "Product created: " . $product->id . PHP_EOL;

    // Create a payment
    $payment = $tahsilat->payments->create([
        'amount' => 99.99,
        'currency' => 'TRY',
        'customer_id' => $customer->id,
        'product_ids' => [$product->id],
        'redirect_url' => 'https://example.com/callback',
        'metadata' => [
            'order_id' => 'order_' . time()
        ]
    ]);

    echo "Payment initiated. Redirect to: " . $payment->redirect_url . PHP_EOL;

} catch (\Tahsilat\Exception\ApiErrorException $e) {
    echo "API Error: " . $e->getMessage() . PHP_EOL;
    if ($e->getErrorCode()) {
        echo "Error Code: " . $e->getErrorCode() . PHP_EOL;
    }
} catch (\Tahsilat\Exception\TahsilatException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
} catch (\Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . PHP_EOL;
}