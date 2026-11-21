<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use PHPUnit\Framework\TestCase;
use Tahsilat\Resource\TransactionResult;
use Tahsilat\Service\TransactionService;
use Tahsilat\Tahsilat;

class TransactionCustomerFieldsTest extends TestCase
{
    private const CUSTOMER = [
        'customer_id' => 43736872748838,
        'name' => 'Ayşe',
        'lastname' => 'Yılmaz',
        'email' => 'ayse@example.com',
        'phone_code' => '+90',
        'phone' => '5320000000',
        'country' => 'TR',
        'city' => 'İstanbul',
        'district' => null,
        'address' => null,
        'zip_code' => null,
        'metadata' => [
            [
                'merchant_id' => '1234567',
                'key' => 'cari_kodu',
                'value' => 'c-120',
                'created_at' => '2026-11-21T17:52:16+03:00',
                'updated_at' => '2026-11-21T17:52:16+03:00',
            ],
        ],
    ];

    protected function tearDown(): void
    {
        Tahsilat::reset();
    }

    public function testRetrievedTransactionExposesCustomer(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient('sk_test_customerkey', $fake);
        $client->setAccessToken('tok_ready');
        $service = new TransactionService($client, $fake);

        $fake->queue(['status' => true, 'data' => [
            'transaction_id' => 40973352446561,
            'amount' => 10000,
            'metadata' => [['key' => 'order_id', 'value' => 'ORD-10045']],
            'customer' => self::CUSTOMER,
        ]]);

        $transaction = $service->retrieve(40973352446561);

        $this->assertTrue($transaction->hasCustomer());
        $this->assertSame(43736872748838, $transaction->getCustomerId());
        $this->assertSame('Ayşe', $transaction->getCustomer()['name']);
        $this->assertSame('ayse@example.com', $transaction->customer['email']);
        $this->assertSame('c-120', $transaction->getCustomerMetadataValue('cari_kodu'));
        $this->assertSame(['cari_kodu' => 'c-120'], $transaction->getCustomerMetadataAsKeyValue());
        $this->assertSame(['order_id' => 'ORD-10045'], $transaction->getMetadataAsKeyValue());
    }

    public function testCustomerWithoutMetadata(): void
    {
        $customer = self::CUSTOMER;
        $customer['metadata'] = [];

        $transaction = new TransactionResult(['transaction_id' => 1, 'customer' => $customer]);

        $this->assertTrue($transaction->hasCustomer());
        $this->assertSame([], $transaction->getCustomerMetadataAsKeyValue());
        $this->assertSame('fallback', $transaction->getCustomerMetadataValue('cari_kodu', 'fallback'));
    }

    public function testNullOrAbsentCustomerIsHandled(): void
    {
        $withNull = new TransactionResult(['transaction_id' => 1, 'customer' => null]);
        $withoutKey = new TransactionResult(['transaction_id' => 1]);

        foreach ([$withNull, $withoutKey] as $transaction) {
            $this->assertFalse($transaction->hasCustomer());
            $this->assertNull($transaction->getCustomer());
            $this->assertNull($transaction->getCustomerId());
            $this->assertSame([], $transaction->getCustomerMetadataAsKeyValue());
            $this->assertNull($transaction->getCustomerMetadataValue('cari_kodu'));
        }
    }
}
