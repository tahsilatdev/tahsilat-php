<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

/**
 * Payment resource
 *
 * @property int|null $transaction_id Payment Transaction ID
 * @property string|null $payment_page_url Redirect Payment Page URL for 3DS
 * @property string|null $expires_at Expiration timestamp
 *
 * @package Tahsilat\Resource
 */
class Payment extends ApiResource
{
}
