<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use PHPUnit\Framework\TestCase;
use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Service\CommissionService;
use Tahsilat\Tahsilat;

class TokenLifecycleTest extends TestCase
{
    private const API_KEY = 'sk_test_lifecyclekey';

    protected function tearDown(): void
    {
        Tahsilat::reset();
    }

    /**
     * @return array<string, mixed>
     */
    private function tokenResponse(string $token, int $ttlSeconds = 600): array
    {
        return [
            'status' => true,
            'data' => [
                'access_token' => $token,
                'expires_at' => date('c', time() + $ttlSeconds),
            ],
        ];
    }

    public function testConstructorMakesNoRequests(): void
    {
        $fake = new FakeHttpClient();
        new TestableTahsilatClient(self::API_KEY, $fake);

        $this->assertCount(0, $fake->calls, 'Client construction must not mint a token');
    }

    public function testTokenIsMintedLazilyOnFirstCallWithCorrectBearers(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient(self::API_KEY, $fake);

        $fake->queue($this->tokenResponse('tok_1'));
        $fake->queue(['status' => true, 'data' => []]);

        $service = new CommissionService($client, $fake);
        $service->search();

        $this->assertCount(2, $fake->calls);
        $this->assertStringContainsString('token/get-token', $fake->calls[0]['url']);
        $this->assertSame('Bearer ' . self::API_KEY, $fake->calls[0]['headers']['Authorization']);
        $this->assertStringContainsString('pos/commissions', $fake->calls[1]['url']);
        $this->assertSame('Bearer tok_1', $fake->calls[1]['headers']['Authorization']);
    }

    public function testSecondCallReusesUnexpiredToken(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient(self::API_KEY, $fake);
        $service = new CommissionService($client, $fake);

        $fake->queue($this->tokenResponse('tok_1'));
        $fake->queue(['status' => true, 'data' => []]);
        $service->search();

        $fake->queue(['status' => true, 'data' => []]);
        $service->search();

        $tokenCalls = array_filter($fake->calls, static fn (array $c): bool => strpos($c['url'], 'get-token') !== false);
        $this->assertCount(1, $tokenCalls, 'A valid token must be reused, not re-minted per call');
    }

    public function testNearlyExpiredTokenIsRefreshedBeforeUse(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient(self::API_KEY, $fake);
        $service = new CommissionService($client, $fake);

        // Expires in 30s — inside the 60s safety margin.
        $fake->queue($this->tokenResponse('tok_short', 30));
        $fake->queue(['status' => true, 'data' => []]);
        $service->search();

        $fake->queue($this->tokenResponse('tok_fresh'));
        $fake->queue(['status' => true, 'data' => []]);
        $service->search();

        $this->assertSame('Bearer tok_fresh', $fake->calls[3]['headers']['Authorization']);
    }

    public function testUnauthorizedResponseMintsFreshTokenAndRetriesOnce(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient(self::API_KEY, $fake);
        $service = new CommissionService($client, $fake);

        $fake->queue($this->tokenResponse('tok_stale'));
        $fake->queueHttpError(401, 'Unauthenticated');
        $fake->queue($this->tokenResponse('tok_new'));
        $fake->queue(['status' => true, 'data' => []]);

        $service->search();

        $this->assertCount(4, $fake->calls);
        $this->assertSame('Bearer tok_new', $fake->calls[3]['headers']['Authorization']);
    }

    public function testSecondConsecutiveUnauthorizedPropagates(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient(self::API_KEY, $fake);
        $service = new CommissionService($client, $fake);

        $fake->queue($this->tokenResponse('tok_1'));
        $fake->queueHttpError(401);
        $fake->queue($this->tokenResponse('tok_2'));
        $fake->queueHttpError(401);

        $this->expectException(ApiErrorException::class);
        $service->search();
    }

    public function testNonAuthErrorsAreNotRetried(): void
    {
        $fake = new FakeHttpClient();
        $client = new TestableTahsilatClient(self::API_KEY, $fake);
        $service = new CommissionService($client, $fake);

        $fake->queue($this->tokenResponse('tok_1'));
        $fake->queueHttpError(422, 'validation');

        try {
            $service->search();
            $this->fail('Expected ApiErrorException');
        } catch (ApiErrorException $exception) {
            $this->assertSame(422, $exception->getHttpStatus());
        }

        $this->assertCount(2, $fake->calls, 'A 422 must not trigger a token refresh or retry');
    }
}
