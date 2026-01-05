# Tahsilat PHP SDK

Tahsilat Payment Gateway için resmi PHP SDK.

## Gereksinimler

- PHP 5.4 veya üzeri
- cURL extension
- JSON extension
- mbstring extension

## Kurulum

Composer ile kurulum:
```bash
composer require tahsilat/tahsilat-php
```

Veya manuel olarak dahil edin:
```php
require_once '/path/to/tahsilat-php/init.php';
```

## Hızlı Başlangıç

### Client Başlatma
```php
$tahsilat = new \Tahsilat\TahsilatClient('sk_test_YOUR_SECRET_KEY');
```

> **Önemli:** Sadece secret key'ler (`sk_test_*` veya `sk_live_*`) kabul edilir. Public key'ler (`pk_*`) server-side API çağrıları için kullanılamaz.

### Müşteri Oluşturma
```php
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
        'source' => 'website'
    ]
]);

echo $customer->id;
```

### Ürün Oluşturma
```php
$product = $tahsilat->products->create([
    'product_name' => 'Premium Subscription',
    'price' => 9999, // kuruş cinsinden (99.99 TL)
    'description' => 'Aylık premium üyelik',
    'metadata' => [
        'category' => 'subscription'
    ]
]);

echo $product->id;
```

### Ödeme Oluşturma

#### Ürün Bilgileri ile
```php
$payment = $tahsilat->payments->create([
    'amount' => 20000, // kuruş cinsinden (200.00 TL)
    'currency' => 'TRY',
    'redirect_url' => 'https://example.com/payment/callback',
    'products' => [
        [
            'product_name' => 'Ürün 1',
            'price' => 10000,
            'description' => 'Birinci ürün'
        ],
        [
            'product_name' => 'Ürün 2',
            'price' => 10000,
            'description' => 'İkinci ürün'
        ]
    ],
    'metadata' => [
        'order_id' => 'order_12345'
    ],
    'description' => 'Sipariş #12345'
]);

echo $payment->payment_page_url; // Ödeme sayfası URL'i
echo $payment->transaction_id;   // İşlem ID
```

#### Kayıtlı Ürün ID'leri ile
```php
$payment = $tahsilat->payments->create([
    'amount' => 20000,
    'currency' => 'TRY',
    'redirect_url' => 'https://example.com/payment/callback',
    'product_ids' => [55437751141488, 84920468860151],
    'customer_id' => 20585467989184
]);
```

#### White Label (Kart Bilgileri ile)
```php
$payment = $tahsilat->payments->create([
    'amount' => 20000,
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
            'product_name' => 'Test Ürün',
            'price' => 20000,
            'description' => 'Test açıklaması'
        ]
    ]
]);
```

### İşlem Sorgulama
```php
$transaction = $tahsilat->transactions->retrieve(78810412652494);

echo $transaction->transaction_id;
echo $transaction->payment_status_text; // success, fail, incomplete
echo $transaction->transaction_status_text; // completed, pending, cancelled
echo $transaction->amount;
echo $transaction->formatted_amount;

// Başarı kontrolü
if ($transaction->isSuccess()) {
    echo "Ödeme başarılı!";
}

if ($transaction->isFail()) {
    echo "Ödeme başarısız: " . $transaction->transaction_message;
}
```

### İade İşlemi
```php
$refund = $tahsilat->transactions->refund([
    'transaction_id' => 78810412652494,
    'amount' => 5000, // Kısmi iade (50.00 TL)
    'description' => 'Müşteri talebi ile iade'
]);

if ($refund->isSuccess()) {
    echo $refund->getMessage(); // "İade talebi başarıyla oluşturuldu..."
}
```

### BIN Sorgulama
```php
$bin = $tahsilat->binLookup->detail([
    'bin' => '489455'
]);

echo $bin->bank_name;
echo $bin->card_type;    // credit, debit
echo $bin->card_brand;   // visa, mastercard
```

### Komisyon Sorgulama
```php
$commissions = $tahsilat->commissions->search([
    'amount' => 10000,
    'currency' => 'TRY',
    'bin' => '489455'
]);
```

## Response Kullanımı

Tüm API yanıtları resource objeleri olarak döner. Bu objeler üzerinde çeşitli metodlar kullanabilirsiniz:
```php
$transaction = $tahsilat->transactions->retrieve(78810412652494);

// Tek değer alma
echo $transaction->amount;
echo $transaction->get('amount');
echo $transaction->get('nonexistent_field', 'default_value');

// Tüm veriyi array olarak alma
$data = $transaction->toArray();
print_r($data);

// JSON olarak alma
echo $transaction->toJson();
echo $transaction->toJson(JSON_PRETTY_PRINT);

// Değer kontrolü
if ($transaction->has('metadata')) {
    // metadata alanı mevcut
}

if ($transaction->isNull('transaction_code')) {
    // transaction_code null
}
```

## Konfigürasyon
```php
$tahsilat = new \Tahsilat\TahsilatClient('sk_test_YOUR_SECRET_KEY', [
    'max_retries' => 5,
    'timeout' => 120,
    'connect_timeout' => 60,
    'verify_ssl_certs' => true
]);

// Veya setter ile
$tahsilat->setConfig('timeout', 120);
```

## Hata Yönetimi
```php
use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Exception\InvalidRequestException;
use Tahsilat\Exception\NetworkException;
use Tahsilat\Exception\TahsilatException;

try {
    $payment = $tahsilat->payments->create([
        'amount' => 10000,
        'currency' => 'TRY'
    ]);
} catch (AuthenticationException $e) {
    // Geçersiz API key
    echo 'Kimlik doğrulama hatası: ' . $e->getMessage();
} catch (InvalidRequestException $e) {
    // Geçersiz istek (örn: işlem bulunamadı)
    echo 'Geçersiz istek: ' . $e->getMessage();
} catch (ApiErrorException $e) {
    // API hatası
    echo 'API Hatası: ' . $e->getMessage();
    echo 'Hata Kodu: ' . $e->getErrorCode();
    print_r($e->getResponseData());
} catch (NetworkException $e) {
    // Ağ hatası
    echo 'Ağ Hatası: ' . $e->getMessage();
} catch (TahsilatException $e) {
    // Genel SDK hatası
    echo 'Hata: ' . $e->getMessage();
}
```

## API Key Türleri

| Key Türü | Format | Kullanım |
|----------|--------|----------|
| Secret Test | `sk_test_*` | Test ortamı - tam erişim |
| Secret Live | `sk_live_*` | Canlı ortam - tam erişim |

> **Not:** Public key'ler (`pk_test_*`, `pk_live_*`) bu SDK ile kullanılamaz. Client-side işlemler için JavaScript SDK kullanın.

## Webhook Doğrulama
```php
use Tahsilat\Util\Webhook;

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_TAHSILAT_SIGNATURE'];
$webhookSecret = 'whsec_your_webhook_secret';

try {
    $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
    
    switch ($event->type) {
        case 'payment.success':
            $transaction = $event->data;
            // Ödeme başarılı işlemleri
            break;
        case 'payment.failed':
            // Ödeme başarısız işlemleri
            break;
        case 'refund.completed':
            // İade tamamlandı işlemleri
            break;
    }
    
    http_response_code(200);
} catch (\Tahsilat\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    echo 'Geçersiz imza';
}
```

## PHP Sürüm Uyumluluğu

Bu SDK PHP 5.4'ten PHP 8.4'e kadar tüm sürümlerle uyumludur.

| PHP Sürümü | Durum |
|------------|-------|
| 5.4 - 5.6 | ✅ Destekleniyor |
| 7.0 - 7.4 | ✅ Destekleniyor |
| 8.0 - 8.4 | ✅ Destekleniyor |

## Lisans

MIT License - detaylar için LICENSE dosyasına bakın.

## Destek

- Dokümantasyon: [https://docs.tahsilat.com](https://docs.tahsilat.com)
- E-posta: destek@tahsilat.com