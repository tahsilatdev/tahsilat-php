<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

use Tahsilat\Util\StatusConstants;

/**
 * Subscription cancellation resource
 *
 * @property int|null $subscription_id Subscription ID
 * @property int|null $status Subscription status code after the call
 * @property string|null $status_text Subscription status text
 * @property string|null $cancelled_at Cancellation timestamp (ISO 8601)
 *
 * @package Tahsilat\Resource
 */
class SubscriptionCancellation extends ApiResource
{
    /**
     * Check if the subscription is cancelled
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return (int) $this->get('status', 0) === StatusConstants::SUBSCRIPTION_STATUS_CANCELLED;
    }
}
