<?php

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\Commission;

/**
 * Service for product operations
 *
 * @package Tahsilat\Service
 */
class CommissionService extends AbstractService
{
    /**
     * Get commission details
     *
     * @param array<string, mixed> $params Commission parameters
     * @param array<string, mixed> $opts Request options
     * @return Commission Commission resource
     * @throws AuthenticationException
     */
    public function search($params = [], $opts = [])
    {
        $response = $this->request('get', '/pos/commissions', $params, $opts);

        return new Commission($response);
    }
}