<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

/**
 * Refund resource
 *
 * @property int|null $transaction_id Transaction ID
 * @property int|null $amount Refund amount (in kuruş/cents)
 * @property string|null $description Refund description
 *
 * @package Tahsilat\Resource
 */
class Refund extends ApiResource
{
}
