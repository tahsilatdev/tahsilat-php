<?php

declare(strict_types=1);

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\Payment;
use JsonException;

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
     * @throws AuthenticationException
     * @throws JsonException
     */
    public function create3ds(array $params = [], array $opts = []): Payment
    {
        // Convert products array to JSON if provided
        if (isset($params['products']) && is_array($params['products'])) {
            $params['products'] = json_encode($params['products'], JSON_THROW_ON_ERROR);
        }

        // Convert metadata to indexed key-value array format
        if (isset($params['metadata']) && is_array($params['metadata'])) {
            $formattedMetadata = [];
            $index = 0;
            foreach ($params['metadata'] as $key => $value) {
                $formattedMetadata[$index] = [
                    'key' => (string) $key,
                    'value' => (string) $value
                ];
                $index++;
            }
            $params['metadata'] = $formattedMetadata;
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
     * @throws AuthenticationException
     * @throws JsonException
     */
    public function create(array $params = [], array $opts = []): Payment
    {
        return $this->create3ds($params, $opts);
    }
}
