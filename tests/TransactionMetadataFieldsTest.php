<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use PHPUnit\Framework\TestCase;
use Tahsilat\Resource\TransactionResult;
use Tahsilat\Resource\WebhookEvent;
use Tahsilat\Service\TransactionService;
use Tahsilat\Tahsilat;
use Tahsilat\Util\Webhook;

class TransactionMetadataFieldsTest extends TestCase
{
    private const METADATA = [
        [
            'merchant_id' => '1234567',
            'key' => 'order_id',
            'value' => 'ORD-10045',
            'created_at' => '2026-11-06T06:14:37+03:00',
            'updated_at' => '2026-11-06T06:14:37+03:00',
        ],
        [
            'merchant_id' => '1234567',
            'key' => 'channel',
            'value' => 'web',
            'created_at' => '2026-11-06T06:14:37+03:00',
            'updated_at' => '2026-11-06T06:14:37+03:00',
        ],
    ];

    protected function tearDown(): void
    {
        Tahsilat::reset();
    }

    /**
     * @return array<string, mixed>
     */
    private function transactionPayload(array $metadata): array
    {
        return [
            'transaction_id' => 40973352446561,
            'amount' => 10000,
            'charged_amount' => 10000,
            'currency_code' => 'TRY',
            'installment_count' => 1,
            'payment_status' => 1,
            'payment_status_text' => 'success',
            'transaction_status' => 2,
            'transaction_status_text' => 'completed',
            'transaction_message' => null,
            'transaction_code' => 'A12345',
            'payment_method' => 1,
            'payment_method_text' => 'is_3d',
            'pre_auth' => false,
            'created_at' => '2026-11-06T06:14:37+03:00',
            'start_at' => '2026-11-06T06:14:37+03:00',
            'end_at' => '2026-11-06T06:14:39+03:00',
            'metadata' => $metadata,
            'subscription' => null,
            'formatted_amount' => '100.00',
            'formatted_charged_amount' => '100.00',
        ];
    }

    public function testRetrievedTransactionExposesMetadata(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient('sk_test_metadatakey', $fake);
        $client->setAccessToken('tok_ready');
        $service = new TransactionService($client, $fake);

        $fake->queue(['status' => true, 'data' => $this->transactionPayload(self::METADATA)]);

        $transaction = $service->retrieve(40973352446561);

        $this->assertInstanceOf(TransactionResult::class, $transaction);
        $this->assertTrue($transaction->has('metadata'));
        $this->assertCount(2, $transaction->metadata);
        $this->assertSame('ORD-10045', $transaction->getMetadataValue('order_id'));
        $this->assertSame('web', $transaction->getMetadataValue('channel'));
        $this->assertSame(['order_id' => 'ORD-10045', 'channel' => 'web'], $transaction->getMetadataAsKeyValue());
    }

    public function testSignedWebhookEventExposesMetadata(): void
    {
        $secret = 'whsec_metadata_test';
        $payload = json_encode($this->transactionPayload(self::METADATA));
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        $event = Webhook::constructEvent($payload, 't=' . $timestamp . ',v1=' . $signature, $secret);

        $this->assertInstanceOf(WebhookEvent::class, $event);
        $this->assertSame('ORD-10045', $event->getMetadataValue('order_id'));
        $this->assertSame(['order_id' => 'ORD-10045', 'channel' => 'web'], $event->getMetadataAsKeyValue());
    }

    public function testUnknownKeyFallsBackToDefault(): void
    {
        $payload = $this->transactionPayload(self::METADATA);

        foreach ([new TransactionResult($payload), new WebhookEvent($payload)] as $resource) {
            $this->assertNull($resource->getMetadataValue('missing'));
            $this->assertSame('fallback', $resource->getMetadataValue('missing', 'fallback'));
        }
    }

    public function testEmptyOrAbsentMetadataIsHandled(): void
    {
        $empty = $this->transactionPayload([]);
        $absent = $this->transactionPayload([]);
        unset($absent['metadata']);

        foreach ([new TransactionResult($empty), new WebhookEvent($empty), new TransactionResult($absent), new WebhookEvent($absent)] as $resource) {
            $this->assertSame([], $resource->getMetadataAsKeyValue());
            $this->assertSame('fallback', $resource->getMetadataValue('order_id', 'fallback'));
        }
    }
}
