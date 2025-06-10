<?php

namespace Tahsilat\Resource;

use Tahsilat\Util\StatusConstants;

/**
 * Webhook event resource
 *
 * @property int $transaction_id Transaction ID
 * @property float $amount Transaction amount
 * @property int $installment_count Installment count
 * @property int $payment_status Payment status
 * @property string $payment_status_text Payment status text
 * @property int $transaction_status Transaction status
 * @property string $transaction_status_text Transaction status text
 * @property string|null $transaction_message Transaction message
 * @property string|null $transaction_code Transaction code
 * @property string $currency_code Currency code
 * @property int $payment_method Payment method
 * @property string $payment_method_text Payment method text
 * @property bool $pre_auth Pre-authorization flag
 * @property string $created_at Creation timestamp
 * @property string|null $start_at Start timestamp
 * @property string|null $end_at End timestamp
 * @property array $metadata Transaction metadata
 *
 * @package Tahsilat\Resource
 */
class WebhookEvent extends ApiResource
{
    /**
     * Check if payment was successful
     *
     * @return bool Whether payment succeeded
     */
    public function isSuccess()
    {
        return $this->payment_status === StatusConstants::PAYMENT_STATUS_SUCCESS &&
            in_array($this->transaction_status, [
                StatusConstants::TRANSACTION_STATUS_COMPLETED,
                StatusConstants::TRANSACTION_STATUS_PRE_AUTH
            ]);
    }

    /**
     * Check if payment failed
     *
     * @return bool Whether payment failed
     */
    public function isFail()
    {
        return $this->payment_status === StatusConstants::PAYMENT_STATUS_FAILED;
    }

    /**
     * Get the transaction ID
     *
     * @return int Transaction ID
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }
}