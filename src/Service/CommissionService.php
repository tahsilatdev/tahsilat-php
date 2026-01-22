<?php

declare(strict_types=1);

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\Commission;

/**
 * Service for commission operations
 *
 * @package Tahsilat\Service
 */
class CommissionService extends AbstractService
{
    /**
     * Search commission details
     *
     * @param array<string, mixed> $params Commission parameters
     * @param array<string, mixed> $opts Request options
     * @return Commission Commission resource
     * @throws AuthenticationException
     */
    public function search(array $params = [], array $opts = []): Commission
    {
        $response = $this->request('get', '/pos/commissions', $params, $opts);

        return new Commission($response);
    }
}
