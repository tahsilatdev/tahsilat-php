<?php

namespace Tahsilat\Service;

use Tahsilat\Resource\Payment;

/**
 * Service for payment operations
 *
 * @package Tahsilat\Service
 */
class PaymentService extends AbstractService
{
    /**
     * Create a 3DS payment
     *
     * @param array<string, mixed> $params Payment parameters
     * @param array<string, mixed> $opts Request options
     * @return Payment Payment resource
     */
    public function create3ds($params = [], $opts = [])
    {
        // Convert products array to JSON if provided
        if (isset($params['products']) && is_array($params['products'])) {
            $params['products'] = json_encode($params['products']);
        }

        // Convert metadata to JSON if provided
        if (isset($params['metadata']) && is_array($params['metadata'])) {
            $params['metadata'] = json_encode($params['metadata']);
        }

        // Make request
        $response = $this->request('post', '/payment/3ds', $params, $opts);

        return new Payment($response);
    }

    /**
     * Create a payment (alias for create3ds)
     *
     * @param array<string, mixed> $params Payment parameters
     * @param array<string, mixed> $opts Request options
     * @return Payment Payment resource
     */
    public function create($params = [], $opts = [])
    {
        return $this->create3ds($params, $opts);
    }
}