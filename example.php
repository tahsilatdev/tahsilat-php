<?php

/**
 * Tahsilat PHP SDK Example
 *
 * Bu dosya SDK'nın temel kullanımını gösterir.
 *
 * @package Tahsilat
 */

// Composer autoloader kullanıyorsanız:
// require_once __DIR__ . '/vendor/autoload.php';

// Manuel kurulum için:
require_once __DIR__ . '/init.php';

use Tahsilat\TahsilatClient;
use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Exception\InvalidRequestException;
use Tahsilat\Exception\NetworkException;
use Tahsilat\Exception\TahsilatException;

// API anahtarınızı buraya girin
$apiKey = 'sk_test_YOUR_SECRET_KEY';

try {
    // Client oluştur
    $tahsilat = new TahsilatClient($apiKey, [
        'timeout' => 120,
        'max_retries' => 3,
    ]);

    echo "SDK Başarıyla başlatıldı!\n";
    echo "Ortam: " . ($tahsilat->getConfig('verify_ssl_certs') ? 'Production' : 'Development') . "\n\n";

    // Örnek: Müşteri oluşturma
    /*
    $customer = $tahsilat->customers->create([
        'name' => 'John',
        'lastname' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '5551234567',
        'country' => 'TR',
        'city' => 'Istanbul',
        'district' => 'Kadıköy',
        'address' => '123 Main St',
        'zip_code' => '34710',
        'metadata' => [
            'customer_type' => 'premium',
            'source' => 'sdk_example'
        ]
    ]);

    echo "Müşteri oluşturuldu: " . $customer->id . "\n";
    */

    // Örnek: Ödeme oluşturma
    /*
    $payment = $tahsilat->payments->create([
        'amount' => 10000, // 100.00 TL
        'currency' => 'TRY',
        'redirect_url' => 'https://example.com/payment/callback',
        'products' => [
            [
                'product_name' => 'Test Ürünü',
                'price' => 10000,
                'description' => 'Test açıklaması'
            ]
        ],
        'metadata' => [
            'order_id' => 'test_order_' . time()
        ]
    ]);

    echo "Ödeme sayfası: " . $payment->payment_page_url . "\n";
    echo "Transaction ID: " . $payment->transaction_id . "\n";
    */

    // Örnek: İşlem sorgulama
    /*
    $transaction = $tahsilat->transactions->retrieve(78810412652494);

    echo "Transaction Status: " . $transaction->transaction_status_text . "\n";
    echo "Payment Status: " . $transaction->payment_status_text . "\n";
    echo "Amount: " . ($transaction->amount / 100) . " " . $transaction->currency_code . "\n";

    if ($transaction->isSuccess()) {
        echo "Ödeme başarılı!\n";
    }

    if ($transaction->isFail()) {
        echo "Ödeme başarısız: " . $transaction->transaction_message . "\n";
    }
    */

    // Örnek: BIN sorgulama
    /*
    $bin = $tahsilat->binLookup->detail([
        'bin_number' => '48945540'
    ]);

    echo "Banka: " . $bin->bank_name . "\n";
    echo "Kart Tipi: " . $bin->card_type . "\n";
    echo "Kart Markası: " . $bin->card_brand . "\n";
    */

} catch (AuthenticationException $e) {
    echo "Kimlik doğrulama hatası: " . $e->getMessage() . "\n";
} catch (InvalidRequestException $e) {
    echo "Geçersiz istek: " . $e->getMessage() . "\n";
} catch (ApiErrorException $e) {
    echo "API Hatası: " . $e->getMessage() . "\n";
    echo "Hata Kodu: " . $e->getErrorCode() . "\n";
    
    if ($e->isValidationError()) {
        echo "Validation Errors:\n";
        print_r($e->getValidationErrors());
    }
} catch (NetworkException $e) {
    echo "Ağ Hatası: " . $e->getMessage() . "\n";
} catch (TahsilatException $e) {
    echo "SDK Hatası: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "Beklenmeyen Hata: " . $e->getMessage() . "\n";
}
