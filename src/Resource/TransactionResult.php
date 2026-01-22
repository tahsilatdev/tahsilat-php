<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

use Tahsilat\Util\StatusConstants;

/**
 * Transaction result resource
 *
 * @property int $transaction_id Transaction ID
 * @property int $amount Transaction amount (in kuruş/cents)
 * @property string $currency_code Currency code (TRY, USD, EUR)
 * @property int $installment_count Installment count
 * @property int $payment_status Payment status code
 * @property string $payment_status_text Payment status text (success, fail)
 * @property int $transaction_status Transaction status code
 * @property string $transaction_status_text Transaction status text (completed, pending, cancelled)
 * @property string|null $transaction_message Transaction message if any
 * @property string|null $transaction_code Transaction code if any
 * @property int $payment_method Payment method code
 * @property string $payment_method_text Payment method text (is_3d, is_2d)
 * @property bool $pre_auth Whether pre-authorization
 * @property string $created_at Creation timestamp (ISO 8601)
 * @property string|null $start_at Transaction start time
 * @property string|null $end_at Transaction end time
 * @property array $metadata Transaction metadata
 * @property string $formatted_amount Human-readable amount (e.g., "200.00")
 *
 * @package Tahsilat\Resource
 */
class TransactionResult extends ApiResource
{
    /**
     * Check if payment was successful
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->payment_status === StatusConstants::PAYMENT_STATUS_SUCCESS &&
            in_array($this->transaction_status, [
                StatusConstants::TRANSACTION_STATUS_COMPLETED,
                StatusConstants::TRANSACTION_STATUS_PRE_AUTHORIZED
            ], true);
    }

    /**
     * Check if payment failed
     *
     * @return bool
     */
    public function isFail(): bool
    {
        return $this->payment_status === StatusConstants::PAYMENT_STATUS_FAILED;
    }

    /**
     * Check if payment is incomplete
     *
     * @return bool
     */
    public function isIncomplete(): bool
    {
        return $this->payment_status === StatusConstants::PAYMENT_STATUS_INCOMPLETE;
    }

    /**
     * Check if transaction is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_PENDING;
    }

    /**
     * Check if transaction is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_COMPLETED;
    }

    /**
     * Check if transaction is pre-authorized
     *
     * @return bool
     */
    public function isPreAuthorized(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_PRE_AUTHORIZED;
    }

    /**
     * Check if transaction is cancelled
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_CANCELLED;
    }

    /**
     * Check if transaction is refunded (full)
     *
     * @return bool
     */
    public function isRefunded(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_REFUNDED;
    }

    /**
     * Check if transaction is partially refunded
     *
     * @return bool
     */
    public function isPartialRefunded(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_PARTIAL_REFUNDED;
    }

    /**
     * Check if transaction has any refund (full or partial)
     *
     * @return bool
     */
    public function hasRefund(): bool
    {
        return in_array($this->transaction_status, [
            StatusConstants::TRANSACTION_STATUS_REFUNDED,
            StatusConstants::TRANSACTION_STATUS_PARTIAL_REFUNDED
        ], true);
    }

    /**
     * Check if transaction has chargeback
     *
     * @return bool
     */
    public function isChargeback(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_CHARGEBACK;
    }

    /**
     * Check if transaction has partial chargeback
     *
     * @return bool
     */
    public function isPartialChargeback(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_PARTIAL_CHARGEBACK;
    }

    /**
     * Check if transaction has any chargeback (full or partial)
     *
     * @return bool
     */
    public function hasChargeback(): bool
    {
        return in_array($this->transaction_status, [
            StatusConstants::TRANSACTION_STATUS_CHARGEBACK,
            StatusConstants::TRANSACTION_STATUS_PARTIAL_CHARGEBACK
        ], true);
    }

    /**
     * Check if transaction is flagged as fraud
     *
     * @return bool
     */
    public function isFraud(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_FRAUD;
    }

    /**
     * Check if transaction timed out
     *
     * @return bool
     */
    public function isTimeout(): bool
    {
        return $this->transaction_status === StatusConstants::TRANSACTION_STATUS_TIMEOUT;
    }

    /**
     * Check if this is a 3D secure payment
     *
     * @return bool
     */
    public function is3D(): bool
    {
        return $this->payment_method === StatusConstants::PAYMENT_METHOD_3D;
    }

    /**
     * Check if this is a 2D payment
     *
     * @return bool
     */
    public function is2D(): bool
    {
        return $this->payment_method === StatusConstants::PAYMENT_METHOD_2D;
    }

    /**
     * Check if this is a pre-authorization transaction
     *
     * @return bool
     */
    public function isPreAuth(): bool
    {
        return $this->pre_auth === true;
    }

    /**
     * Get the transaction ID
     *
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->transaction_id;
    }

    /**
     * Get amount in decimal format (e.g., 200.00)
     *
     * @return float
     */
    public function getAmountDecimal(): float
    {
        return $this->amount / 100;
    }

    /**
     * Get transaction message
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->transaction_message;
    }

    /**
     * Get metadata value by key
     *
     * @param string $key Metadata key
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function getMetadataValue(string $key, $default = null)
    {
        if (!isset($this->metadata) || !is_array($this->metadata)) {
            return $default;
        }

        foreach ($this->metadata as $item) {
            if (isset($item['key']) && $item['key'] === $key) {
                return $item['value'] ?? $default;
            }
        }

        return $default;
    }

    /**
     * Get all metadata as key-value pairs
     *
     * @return array<string, string>
     */
    public function getMetadataAsKeyValue(): array
    {
        if (!isset($this->metadata) || !is_array($this->metadata)) {
            return [];
        }

        $result = [];
        foreach ($this->metadata as $item) {
            if (isset($item['key'])) {
                $result[$item['key']] = $item['value'] ?? null;
            }
        }

        return $result;
    }

    /**
     * Get payment status text in Turkish
     *
     * @return string
     */
    public function getPaymentStatusTextTr(): string
    {
        $statusMap = [
            StatusConstants::PAYMENT_STATUS_SUCCESS => 'Başarılı',
            StatusConstants::PAYMENT_STATUS_FAILED => 'Başarısız',
            StatusConstants::PAYMENT_STATUS_INCOMPLETE => 'Tamamlanmadı',
        ];

        return $statusMap[$this->payment_status] ?? 'Bilinmiyor';
    }

    /**
     * Get transaction status text in Turkish
     *
     * @return string
     */
    public function getStatusTextTr(): string
    {
        $statusMap = [
            StatusConstants::TRANSACTION_STATUS_PENDING => 'Beklemede',
            StatusConstants::TRANSACTION_STATUS_COMPLETED => 'Tamamlandı',
            StatusConstants::TRANSACTION_STATUS_PRE_AUTHORIZED => 'Ön Provizyon',
            StatusConstants::TRANSACTION_STATUS_CANCELLED => 'İptal',
            StatusConstants::TRANSACTION_STATUS_REFUNDED => 'İade',
            StatusConstants::TRANSACTION_STATUS_PARTIAL_REFUNDED => 'Kısmi İade',
            StatusConstants::TRANSACTION_STATUS_CHARGEBACK => 'İtiraz',
            StatusConstants::TRANSACTION_STATUS_PARTIAL_CHARGEBACK => 'Kısmi İtiraz',
            StatusConstants::TRANSACTION_STATUS_FRAUD => 'Şüpheli',
            StatusConstants::TRANSACTION_STATUS_TIMEOUT => 'Zaman Aşımı',
        ];

        return $statusMap[$this->transaction_status] ?? 'Bilinmiyor';
    }
}