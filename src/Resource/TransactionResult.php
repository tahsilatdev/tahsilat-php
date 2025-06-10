<?php

namespace Tahsilat\Resource;

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
}