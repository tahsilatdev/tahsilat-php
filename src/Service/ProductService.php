<?php

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\Product;

/**
 * Service for product operations
 *
 * @package Tahsilat\Service
 */
class ProductService extends AbstractService
{
    /**
     * Create a new product
     *
     * @param array<string, mixed> $params Product parameters
     * @param array<string, mixed> $opts Request options
     * @return Product Product resource
     * @throws AuthenticationException
     */
    public function create($params = [], $opts = [])
    {
        // Convert metadata to JSON if provided
        if (isset($params['metadata']) && is_array($params['metadata'])) {
            $params['metadata'] = json_encode($params['metadata']);
        }

        $response = $this->request('post', '/products', $params, $opts);

        return new Product($response);
    }
}