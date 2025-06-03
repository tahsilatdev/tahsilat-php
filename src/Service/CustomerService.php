<?php

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\Customer;

/**
 * Service for customer operations
 *
 * @package Tahsilat\Service
 */
class CustomerService extends AbstractService
{
    /**
     * Create a new customer
     *
     * @param array<string, mixed> $params Customer parameters
     * @param array<string, mixed> $opts Request options
     * @return Customer Customer resource
     * @throws AuthenticationException
     */
    public function create($params = [], $opts = [])
    {
        // Convert metadata to JSON if provided
        if (isset($params['metadata']) && is_array($params['metadata'])) {
            $params['metadata'] = json_encode($params['metadata']);
        }

        $response = $this->request('post', '/customers', $params, $opts);

        return new Customer($response);
    }
}