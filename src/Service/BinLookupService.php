<?php

declare(strict_types=1);

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\BinLookup;

/**
 * Service for BIN lookup operations
 *
 * @package Tahsilat\Service
 */
class BinLookupService extends AbstractService
{
    /**
     * Get BIN details
     *
     * @param array<string, mixed> $params BIN lookup parameters
     * @param array<string, mixed> $opts Request options
     * @return BinLookup BinLookup resource
     * @throws AuthenticationException
     */
    public function detail(array $params = [], array $opts = []): BinLookup
    {
        $response = $this->request('get', '/bin-lookup', $params, $opts);

        return new BinLookup($response);
    }
}
