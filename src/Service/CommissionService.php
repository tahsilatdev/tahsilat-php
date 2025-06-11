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
     * Create a new product
     *
     * @param array<string, mixed> $params Commission parameters
     * @param array<string, mixed> $opts Request options
     * @return Commission Commission resource
     * @throws AuthenticationException
     */
    public function create($params = [], $opts = [])
    {
        $response = $this->request('post', '/pos/commissions', $params, $opts);

        return new Commission($response);
    }
}