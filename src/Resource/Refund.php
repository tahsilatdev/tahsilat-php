<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

/**
 * Refund resource
 *
 * @property int|null $id Refund record ID
 * @property int|null $transaction_id Transaction ID
 * @property int|null $amount Requested refund amount (in kuruş/cents)
 * @property int|null $payment_transaction_id Payment transaction ID
 * @property int|null $merchant_id Merchant ID
 * @property int|null $company_pos_credential_id POS credential ID
 * @property int|null $prev_payment_status Payment status before the refund
 * @property int|null $prev_transaction_status Transaction status before the refund
 * @property int|null $refund_amount Refunded amount (in kuruş/cents)
 * @property string|null $formatted_refund_amount Human-readable refunded amount
 * @property float|null $commission_rate Commission rate applied to the refund
 * @property int|null $company_refund_loss Company loss on the refund (in kuruş/cents)
 * @property int|null $platform_profit Platform profit on the refund (in kuruş/cents)
 * @property int|null $refund_charged_amount Amount refunded back to the card (in kuruş/cents)
 * @property string|null $description Refund description
 * @property int|null $status Refund status code
 * @property string|null $status_text Refund status text
 * @property string|null $reject_reason Rejection reason if rejected
 * @property int|null $refund_type Refund type code
 * @property string|null $refund_type_text Refund type text
 * @property string|null $reference_code Bank reference code
 * @property string|null $bank_response_code Bank response code
 * @property string|null $bank_response_message Bank response message
 * @property string|null $bank_response_receive_at Bank response received timestamp
 * @property string|null $created_at Creation timestamp (ISO 8601)
 * @property string|null $updated_at Update timestamp (ISO 8601)
 * @property string|null $formatted_refund_date Human-readable refund date
 * @property string|null $currency_code Currency code (TRY, USD, EUR)
 *
 * @package Tahsilat\Resource
 */
class Refund extends ApiResource
{
}
