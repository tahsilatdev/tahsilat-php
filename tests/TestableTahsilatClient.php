<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use Tahsilat\Service\TokenService;
use Tahsilat\TahsilatClient;

/**
 * Client whose token minting goes through the shared FakeHttpClient, so no
 * test ever touches the network.
 */
class TestableTahsilatClient extends TahsilatClient
{
    public FakeHttpClient $fakeHttpClient;

    /**
     * @param string $apiKey
     * @param FakeHttpClient $fakeHttpClient
     */
    public function __construct(string $apiKey, FakeHttpClient $fakeHttpClient)
    {
        $this->fakeHttpClient = $fakeHttpClient;
        parent::__construct($apiKey);
    }

    /**
     * @return TokenService
     */
    protected function createTokenService(): TokenService
    {
        return new TokenService($this, $this->fakeHttpClient);
    }
}
