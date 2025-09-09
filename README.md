# Tahsilat PHP SDK

A PHP SDK for Tahsilat Payment Gateway.

## Requirements

- PHP 5.6 or higher
- cURL extension
- JSON extension
- mbstring extension

## Installation

Install via Composer:

```bash
composer require tahsilat/tahsilat-php
```

Or manually include the SDK:

```php
require_once '/path/to/tahsilat-php/init.php';
```

## Quick Start

### Initialize the Client

```php
// Using TahsilatClient (recommended)
$tahsilat = new \Tahsilat\TahsilatClient('sk_test_YOUR_API_KEY');

// Or using static configuration
\Tahsilat\Tahsilat::setApiKey('sk_test_YOUR_API_KEY');
```

### Create a Customer

```php
$customer = $tahsilat->customers->create([
    'name' => 'John',
    'lastname' => 'Doe',
    'email' => 'john.doe@example.com',
    'phone' => '5551234567',
    'country' => 'tr',
    'city' => 'Istanbul',
    'metadata' => [
        'customer_type' => 'premium'
    ]
]);

echo $customer->id; // Customer ID
```

### Create a Product

```php
$product = $tahsilat->products->create([
    'product_name' => 'Premium Subscription',
    'price' => 9999,
    'quantity' => 1,
    'description' => 'Monthly premium subscription',
    'metadata' => [
        'category' => 'subscription'
    ]
]);

echo $product->id; // Product ID
```

### Create a Payment

```php
// With products array
$payment = $tahsilat->payments->create([
    'amount' => 19999,
    'currency' => 'TRY',
    'installment_count' => 1,
    'redirect_url' => 'https://example.com/payment/callback',
    'products' => [
        [
            'product_name' => 'Product 1',
            'price' => 9999,
            'quantity' => 1,
            'description' => 'First product'
        ],
        [
            'product_name' => 'Product 2',
            'price' => 10000,
            'quantity' => 1,
            'description' => 'Second product'
        ]
    ],
    'metadata' => [
        'order_id' => 'order_12345'
    ]
]);

// Or with product IDs
$payment = $tahsilat->payments->create([
    'amount' => 19999,
    'currency' => 'TRY',
    'installment_count' => 1,
    'redirect_url' => 'https://example.com/payment/callback',
    'product_ids' => ['55437751141488', '84920468860151'],
    'customer_id' => '20585467989184'
]);

// With white label (card details required)
$payment = $tahsilat->payments->create([
    'amount' => 19999,
    'currency' => 'TRY',
    'white_label' => true,
    'cardholder_name' => 'John Doe',
    'card_number' => '4894554084788683',
    'expiry_month' => '12',
    'expiry_year' => '28',
    'cvv' => '123',
    'redirect_url' => 'https://example.com/payment/callback',
    'products' => [
        [
            'product_name' => 'Test Product',
            'price' => 19999,
            'quantity' => 1,
            'description' => 'Test description'
        ]
    ]
]);

echo $payment->redirect_url; // 3DS redirect URL
```

## Configuration

### Set Custom Configuration

```php
$tahsilat = new \Tahsilat\TahsilatClient('sk_test_YOUR_API_KEY', [
    'max_retries' => 5,
    'timeout' => 120,
    'connect_timeout' => 60,
    'verify_ssl_certs' => true
]);

// Or using setters
$tahsilat->setConfig('timeout', 120);
```

### Using Getter/Setter Methods

```php
// Get API key
$apiKey = $tahsilat->getApiKey();

// Set new API key
$tahsilat->setApiKey('sk_live_NEW_API_KEY');

// Get/Set access token
$token = $tahsilat->getAccessToken();
$tahsilat->setAccessToken('new_access_token');
```

## Error Handling

```php
try {
    $payment = $tahsilat->payments->create([
        'amount' => 10000,
        'currency' => 'TRY'
    ]);
} catch (\Tahsilat\Exception\ApiErrorException $e) {
    echo 'API Error: ' . $e->getMessage();
    echo 'Error Code: ' . $e->getErrorCode();
    print_r($e->getResponseData());
} catch (\Tahsilat\Exception\NetworkException $e) {
    echo 'Network Error: ' . $e->getMessage();
} catch (\Tahsilat\Exception\TahsilatException $e) {
    echo 'General Error: ' . $e->getMessage();
}
```

## API Keys

The SDK supports different API key types:
- `sk_test_*` - Secret key for test environment
- `pk_test_*` - Public key for test environment
- `sk_live_*` - Secret key for production
- `pk_live_*` - Public key for production

Public keys can only initiate payments, while secret keys have full access to all API endpoints.

## License

MIT License - see LICENSE file for details.
