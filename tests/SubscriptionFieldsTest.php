<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use PHPUnit\Framework\TestCase;
use Tahsilat\Resource\Product;
use Tahsilat\Resource\TransactionResult;
use Tahsilat\Resource\WebhookEvent;
use Tahsilat\Service\ProductService;
use Tahsilat\Tahsilat;
use Tahsilat\Util\StatusConstants;

class SubscriptionFieldsTest extends TestCase
{
    protected function tearDown(): void
    {
        Tahsilat::reset();
    }

    public function testProductCreatePassesRecurringFieldsThrough(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient('sk_test_subscriptionkey', $fake);
        $client->setAccessToken('tok_ready');
        $service = new ProductService($client, $fake);

        $fake->queue([
            'status' => true,
            'data' => [
                'id' => 55195540441484,
                'product_name' => 'Premium Üyelik',
                'price' => 49900,
                'is_recurring' => true,
                'billing_interval' => StatusConstants::BILLING_INTERVAL_MONTH,
                'interval_count' => 1,
                'recurring_text' => 'Aylık',
            ],
        ]);

        $product = $service->create([
            'product_name' => 'Premium Üyelik',
            'price' => 49900,
            'is_recurring' => true,
            'billing_interval' => StatusConstants::BILLING_INTERVAL_MONTH,
            'interval_count' => 1,
        ]);

        $sent = $fake->calls[0]['params'];
        $this->assertTrue($sent['is_recurring']);
        $this->assertSame(StatusConstants::BILLING_INTERVAL_MONTH, $sent['billing_interval']);
        $this->assertSame(1, $sent['interval_count']);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertTrue($product->isRecurring());
        $this->assertSame('Aylık', $product->getRecurringText());
    }

    public function testNonRecurringProductHelpers(): void
    {
        $product = new Product(['id' => 1, 'product_name' => 'Tek seferlik', 'price' => 1000]);

        $this->assertFalse($product->isRecurring());
        $this->assertNull($product->getRecurringText());
    }

    public function testSubscriptionBlockIsExposedOnTransactionAndWebhook(): void
    {
        $payload = [
            'transaction_id' => 75690998341504,
            'amount' => 49900,
            'payment_status' => StatusConstants::PAYMENT_STATUS_SUCCESS,
            'transaction_status' => StatusConstants::TRANSACTION_STATUS_COMPLETED,
            'subscription' => [
                'subscription_id' => 19464539815644,
                'status' => StatusConstants::SUBSCRIPTION_STATUS_ACTIVE,
                'cycle_number' => 1,
                'next_billing_at' => '2027-01-05T09:00:03+03:00',
                'cancelled_at' => null,
                'status_text' => 'active',
                'is_initial_payment' => false,
            ],
        ];

        foreach ([new TransactionResult($payload), new WebhookEvent($payload)] as $resource) {
            $this->assertTrue($resource->hasSubscription());
            $this->assertSame(19464539815644, $resource->getSubscriptionId());
            $this->assertSame(1, $resource->getSubscriptionCycleNumber());
            $this->assertFalse($resource->isInitialSubscriptionPayment());
            $this->assertSame('Aktif', $resource->getSubscriptionStatusTextTr());
            $this->assertSame('active', $resource->getSubscription()['status_text']);
        }
    }

    public function testInitialPaymentIsCycleZero(): void
    {
        $event = new WebhookEvent([
            'transaction_id' => 1,
            'subscription' => [
                'subscription_id' => 7,
                'status' => StatusConstants::SUBSCRIPTION_STATUS_PAST_DUE,
                'cycle_number' => 0,
                'is_initial_payment' => true,
            ],
        ]);

        $this->assertTrue($event->isInitialSubscriptionPayment());
        $this->assertSame(0, $event->getSubscriptionCycleNumber());
        $this->assertSame('Ödeme Bekliyor', $event->getSubscriptionStatusTextTr());
    }

    public function testNullSubscriptionMeansNotASubscriptionPayment(): void
    {
        $withNull = new TransactionResult(['transaction_id' => 1, 'subscription' => null]);
        $withoutKey = new TransactionResult(['transaction_id' => 1]);

        foreach ([$withNull, $withoutKey] as $resource) {
            $this->assertFalse($resource->hasSubscription());
            $this->assertNull($resource->getSubscription());
            $this->assertNull($resource->getSubscriptionId());
            $this->assertNull($resource->getSubscriptionCycleNumber());
            $this->assertFalse($resource->isInitialSubscriptionPayment());
            $this->assertNull($resource->getSubscriptionStatusTextTr());
        }
    }
}
