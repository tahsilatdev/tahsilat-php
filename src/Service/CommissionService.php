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
     * The API returns a LIST of commission rows, so this maps each row to its
     * own Commission resource.
     *
     * @param array<string, mixed> $params Commission parameters
     * @param array<string, mixed> $opts Request options
     * @return Commission[] Commission resources
     * @throws AuthenticationException
     */
    public function search(array $params = [], array $opts = []): array
    {
        $response = $this->request('get', '/pos/commissions', $params, $opts);

        $commissions = [];

        foreach ($response as $row) {
            if (is_array($row)) {
                $commissions[] = new Commission($row);
            }
        }

        return $commissions;
    }
}
