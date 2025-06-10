<?php

namespace Tahsilat\Resource;

use Tahsilat\Util\StatusConstants;

/**
 * Transaction result resource
 *
 * @property int $transaction_id Transaction ID
 * @property float $amount Transaction amount
 * @property string $currency_code Currency code (TRY, USD, EUR)
 * @property int $installment_count Installment count
 * @property int $payment_status Payment status code
 * @property string $payment_status_text Payment status text (success, failed)
 * @property int $transaction_status Transaction status code
 * @property string $transaction_status_text Transaction status text (completed, pending)
 * @property string|null $transaction_message Transaction message if any
 * @property string|null $transaction_code Transaction code if any
 * @property int $payment_method Payment method code
 * @property string $payment_method_text Payment method text (is_3d, is_2d)
 * @property bool $pre_auth Whether pre-authorization
 * @property string $created_at Creation timestamp
 * @property string $start_at Transaction start time
 * @property string $end_at Transaction end time
 * @property array $metadata Transaction metadata
 */
class TransactionResult extends ApiResource
{
    /**
     * Check if payment was successful
     *
     * @return bool Whether payment succeeded
     */
    public function isSuccess()
    {
        if (!isset($this->payment_status) || !isset($this->transaction_status)) {
            return false;
        }

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
        if (!isset($this->payment_status)) {
            return false;
        }

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