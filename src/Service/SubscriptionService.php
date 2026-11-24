<?php

declare(strict_types=1);

namespace Tahsilat\Service;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Exception\InvalidRequestException;
use Tahsilat\Resource\SubscriptionCancellation;

/**
 * Service for subscription operations
 *
 * @package Tahsilat\Service
 */
class SubscriptionService extends AbstractService
{
    /**
     * Cancel a subscription
     *
     * @param int|string $subscriptionId Subscription ID
     * @param array<string, mixed> $opts Request options
     * @return SubscriptionCancellation Subscription cancellation resource
     * @throws AuthenticationException
     * @throws ApiErrorException
     * @throws InvalidRequestException
     */
    public function cancel($subscriptionId, array $opts = []): SubscriptionCancellation
    {
        if (empty($subscriptionId)) {
            throw new InvalidRequestException('Subscription ID is required');
        }

        $response = $this->request('post', '/subscriptions/' . rawurlencode((string) $subscriptionId) . '/cancel', [], $opts);

        return new SubscriptionCancellation($response);
    }
}
