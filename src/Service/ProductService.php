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
    public function create($params = array(), $opts = array())
    {
        // Convert metadata to indexed key-value array format
        if (isset($params['metadata']) && is_array($params['metadata'])) {
            $formattedMetadata = array();
            $index = 0;
            foreach ($params['metadata'] as $key => $value) {
                $formattedMetadata[$index] = array(
                    'key' => (string) $key,
                    'value' => (string) $value
                );
                $index++;
            }
            $params['metadata'] = $formattedMetadata;
        }

        $response = $this->request('post', '/products', $params, $opts);

        return new Product($response);
    }
}