<?php

namespace Tahsilat\Resource;

/**
 * Payment resource
 *
 * @property int $id Payment ID
 * @property string $status Payment status
 * @property int $amount Payment amount
 * @property string $currency Payment currency
 * @property int $customer_id Customer ID
 * @property string $redirect_url Redirect URL for 3DS
 * @property array $metadata Payment metadata
 * @property string $created_at Creation timestamp
 *
 * @package Tahsilat\Resource
 */
class Payment extends ApiResource
{
}