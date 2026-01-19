<?php

namespace Tahsilat\Resource;

/**
 * Payment resource
 *
 * @property int $transaction_id Payment Transaction ID
 * @property string $payment_page_url Redirect Payment Page URL for 3DS
 * @property string $expires_at Creation timestamp
 *
 * @package Tahsilat\Resource
 */
class Payment extends ApiResource
{
}