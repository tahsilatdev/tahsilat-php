<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use PHPUnit\Framework\TestCase;
use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\InvalidRequestException;
use Tahsilat\Service\SubscriptionService;
use Tahsilat\Tahsilat;

class SubscriptionCancelTest extends TestCase
{
    private FakeHttpClient $fake;

    private SubscriptionService $service;

    protected function setUp(): void
    {
        $this->fake = new FakeHttpClient();
        $client = new TestableTahsilatClient('sk_test_cancelkey', $this->fake);
        $client->setAccessToken('tok_ready');
        $this->service = new SubscriptionService($client, $this->fake);
    }

    protected function tearDown(): void
    {
        Tahsilat::reset();
    }

    public function testCancelPostsToSubscriptionPathWithoutBody(): void
    {
        $this->fake->queue(['status' => true, 'message' => 'Abonelik iptal edildi.', 'data' => [
            'subscription_id' => 30655095022664,
            'status' => 3,
            'cancelled_at' => '2026-11-21T16:32:56+03:00',
            'status_text' => 'cancelled',
        ]]);

        $cancellation = $this->service->cancel(30655095022664);

        $call = $this->fake->calls[0];
        $this->assertSame('post', $call['method']);
        $this->assertStringEndsWith('/subscriptions/30655095022664/cancel', $call['url']);
        $this->assertSame([], $call['params']);
        $this->assertSame('Bearer tok_ready', $call['headers']['Authorization']);

        $this->assertSame(30655095022664, $cancellation->subscription_id);
        $this->assertSame('cancelled', $cancellation->status_text);
        $this->assertSame('2026-11-21T16:32:56+03:00', $cancellation->cancelled_at);
        $this->assertTrue($cancellation->isCancelled());
    }

    public function testClientExposesSubscriptionsService(): void
    {
        $client = new TestableTahsilatClient('sk_test_cancelkey', $this->fake);

        $this->assertInstanceOf(SubscriptionService::class, $client->subscriptions);
    }

    public function testNonCancelledStatusIsNotReportedAsCancelled(): void
    {
        $this->fake->queue(['status' => true, 'data' => ['subscription_id' => 30655095022664, 'status' => 1]]);

        $this->assertFalse($this->service->cancel(30655095022664)->isCancelled());
    }

    public function testEmptySubscriptionIdIsRejectedBeforeAnyRequest(): void
    {
        $this->expectException(InvalidRequestException::class);

        try {
            $this->service->cancel(0);
        } finally {
            $this->assertSame([], $this->fake->calls);
        }
    }

    public function testUnknownSubscriptionSurfacesApiError(): void
    {
        $this->fake->queueHttpError(422, 'Abonelik bulunamadı.');

        $this->expectException(ApiErrorException::class);

        $this->service->cancel(11111111111111);
    }
}
