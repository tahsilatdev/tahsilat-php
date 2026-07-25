<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use PHPUnit\Framework\TestCase;
use Tahsilat\Tahsilat;

class TahsilatTest extends TestCase
{
    protected function tearDown(): void
    {
        Tahsilat::reset();
    }

    public function testSandboxKeyPrefixSelectsSandboxBase(): void
    {
        $this->assertSame(Tahsilat::API_SANDBOX_BASE, Tahsilat::apiBaseForKey('sk_test_' . str_repeat('a', 74)));
        $this->assertSame(Tahsilat::API_SANDBOX_BASE, Tahsilat::apiBaseForKey('pk_test_' . str_repeat('a', 74)));
    }

    public function testLiveKeySelectsLiveBase(): void
    {
        $this->assertSame(Tahsilat::API_LIVE_BASE, Tahsilat::apiBaseForKey('sk_live_' . str_repeat('a', 74)));
    }

    public function testLiveKeyContainingTestSubstringStaysOnLive(): void
    {
        // Regression: a live key whose random body contains "test" must NOT
        // be routed to the sandbox host.
        $this->assertSame(Tahsilat::API_LIVE_BASE, Tahsilat::apiBaseForKey('sk_live_abctestxyz123'));
    }

    public function testNullKeyDefaultsToLiveBase(): void
    {
        $this->assertSame(Tahsilat::API_LIVE_BASE, Tahsilat::apiBaseForKey(null));
    }

    public function testGetApiBaseUsesStaticKey(): void
    {
        Tahsilat::setApiKey('sk_test_abc');
        $this->assertSame(Tahsilat::API_SANDBOX_BASE, Tahsilat::getApiBase());
        $this->assertTrue(Tahsilat::isSandbox());
    }
}
