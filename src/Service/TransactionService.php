<?php

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\Refund;

/**
 * Service for customer operations
 *
 * @package Tahsilat\Service
 */
class TransactionService extends AbstractService
{
    /**
     * Create a new refund
     *
     * @param array<string, mixed> $params Customer parameters
     * @param array<string, mixed> $opts Request options
     * @return Refund Refund resource
     * @throws AuthenticationException
     */
    public function refund($params = [], $opts = [])
    {
        $response = $this->request('post', '/transaction/refund', $params, $opts);

        return new Refund($response);
    }
}