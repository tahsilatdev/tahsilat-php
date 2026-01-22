# Tahsilat PHP SDK

Tahsilat Payment Gateway için resmi PHP SDK.

> ⚠️ **Önemli:** Bu SDK, güvenlik nedeniyle PHP 7.4 ve üzeri sürümleri desteklemektedir. PHP 7.3 ve altı sürümler bilinen güvenlik açıkları içerdiğinden artık desteklenmemektedir. Eğer eski PHP sürümlerinde kullanmanız gerekiyorsa [v1.1.9](https://github.com/tahsilatdev/tahsilat-php/releases/tag/v1.1.9) sürümünü kullanabilirsiniz, ancak bu sürüm artık güncelleme almamaktadır ve kullanımı önerilmemektedir.

## Gereksinimler

- **PHP 7.4.0 veya üzeri** (güvenlik nedeniyle 7.4'ün altındaki sürümler desteklenmemektedir)
- cURL extension
- JSON extension
- mbstring extension
- OpenSSL extension

## Kurulum

### Composer ile kurulum (Önerilen):
```bash
composer require tahsilat/tahsilat-php
```

### Manuel kurulum:
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

### İşlem Sorgulama
```php
$transaction = $tahsilat->transactions->retrieve(78810412652494);

echo $transaction->transaction_id;
echo $transaction->payment_status_text; // success, fail, incomplete
echo $transaction->transaction_status_text; // completed, pending, cancelled
echo $transaction->amount;

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
```

### BIN Sorgulama
```php
$bin = $tahsilat->binLookup->detail([
    'bin_number' => '489455'
]);

echo $bin->bank_name;
echo $bin->card_type;    // credit, debit
echo $bin->card_brand;   // visa, mastercard
```

### Komisyon Sorgulama
```php
$commissions = $tahsilat->commissions->search();
```

## Response Kullanımı

Tüm API yanıtları resource objeleri olarak döner. Bu objeler üzerinde çeşitli metodlar kullanabilirsiniz:

```php
$transaction = $tahsilat->transactions->retrieve(11810465249113);

// Tek değer alma
echo $transaction->amount;
echo $transaction->get('amount');
echo $transaction->get('transaction_id');

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
$signature = $_SERVER['HTTP_X_TAHSILAT_SIGNATURE'] ?? '';
$webhookSecret = 'whsec_your_webhook_secret';

try {
    $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
    
    // Event tipine göre işlem yapın
    if ($event->isSuccess()) {
        $transactionId = $event->getTransactionId();
        // Ödeme başarılı işlemleri
    }
    
    if ($event->isFail()) {
        // Ödeme başarısız işlemleri
    }
    
    http_response_code(200);
} catch (\Tahsilat\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    echo 'Geçersiz imza';
}
```

## PHP Sürüm Uyumluluğu

Bu SDK PHP 7.4'den PHP 8.4'e kadar tüm sürümlerle uyumludur.

| PHP Sürümü | Durum |
|------------|-------|
| 5.x - 7.3  | ❌ Desteklenmiyor |
| 7.4 - 8.5  | ✅ Destekleniyor |

## Güvenlik

Bu SDK aşağıdaki güvenlik önlemlerini içerir:

- **Minimum PHP 7.4 gereksinimi**
- **SSL sertifika doğrulama** - Varsayılan olarak aktif
- **Timing-safe string comparison** - Webhook imza doğrulamasında timing attack koruması
- **Header injection koruması** - HTTP header'larında CR/LF karakterleri temizlenir
- **SSRF koruması** - HTTP redirect'ler devre dışı

## Lisans

MIT License - detaylar için LICENSE dosyasına bakın.

## Destek

- Dokümantasyon: [https://docs.tahsilat.com](https://docs.tahsilat.com)
- E-posta: info@tahsilat.com
