<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use PHPUnit\Framework\TestCase;
use Tahsilat\Resource\Commission;
use Tahsilat\Service\CommissionService;
use Tahsilat\Tahsilat;

class CommissionServiceTest extends TestCase
{
    private FakeHttpClient $fake;

    private CommissionService $service;

    protected function setUp(): void
    {
        $this->fake = new FakeHttpClient();
        $client = new TestableTahsilatClient('sk_test_commissionkey', $this->fake);
        $client->setAccessToken('tok_ready');
        $this->service = new CommissionService($client, $this->fake);
    }

    protected function tearDown(): void
    {
        Tahsilat::reset();
    }

    public function testSearchMapsEachRowToItsOwnResource(): void
    {
        $this->fake->queue([
            'status' => true,
            'data' => [
                ['merchant_id' => 1, 'installment' => 1, 'installment_text' => 'Tek çekim', 'commission_rate' => 1.99, 'commission_by' => 1],
                ['merchant_id' => 1, 'installment' => 2, 'installment_text' => '2 Taksit', 'commission_rate' => 2.49, 'commission_by' => 2],
            ],
        ]);

        $commissions = $this->service->search();

        $this->assertCount(2, $commissions);
        $this->assertContainsOnlyInstancesOf(Commission::class, $commissions);
        $this->assertSame(1.99, $commissions[0]->commission_rate);
        $this->assertTrue($commissions[0]->isPaidByMerchant());
        $this->assertTrue($commissions[1]->isPaidByCustomer());
    }

    public function testNullDataEnvelopeYieldsEmptyList(): void
    {
        // Regression: {"status":true,"data":null} previously leaked the whole
        // envelope into the resource.
        $this->fake->queue(['status' => true, 'message' => 'ok', 'data' => null]);

        $this->assertSame([], $this->service->search());
    }
}
