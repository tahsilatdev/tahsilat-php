<?php

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\BinLookup;

/**
 * Service for product operations
 *
 * @package Tahsilat\Service
 */
class BinLookupService extends AbstractService
{
    /**
     * Get commission details
     *
     * @param array<string, mixed> $params Commission parameters
     * @param array<string, mixed> $opts Request options
     * @return BinLookup BinLookup resource
     * @throws AuthenticationException
     */
    public function search($params = [], $opts = [])
    {
        $response = $this->request('post', '/bin-lookup', $params, $opts);

        return new BinLookup($response);
    }
}