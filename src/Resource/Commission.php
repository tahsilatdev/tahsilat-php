<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

/**
 * Commission resource
 *
 * @property int $merchant_id Merchant ID
 * @property int|null $card_family_id Card family ID
 * @property int|null $card_segment_type_id Card segment type ID
 * @property int|null $installment Installment count
 * @property string|null $installment_text Human-readable installment text (e.g. "Tek çekim", "2 Taksit")
 * @property float|null $commission_rate Commission rate percentage
 * @property int|null $commission_by Commission payer (1: Merchant, 2: Customer)
 * @property string|null $commission_by_text Commission payer text ("Üye İşyeri" or "Müşteri")
 * @property string $created_at Creation timestamp (ISO 8601)
 * @property string|null $updated_at Update timestamp (ISO 8601)
 * @property array|null $card_family Card family details (name, slug, logo_url, timestamps)
 * @property array|null $card_segment_type Card segment type details (name, slug, timestamps)
 * @property array|null $company_pos_credential POS credential details
 *
 * @package Tahsilat\Resource
 */
class Commission extends ApiResource
{
    /**
     * Commission payer constants
     */
    public const COMMISSION_BY_MERCHANT = 1;
    public const COMMISSION_BY_CUSTOMER = 2;

    /**
     * Check if commission is paid by a merchant
     *
     * @return bool
     */
    public function isPaidByMerchant(): bool
    {
        return ($this->commission_by ?? null) === self::COMMISSION_BY_MERCHANT;
    }

    /**
     * Check if commission is paid by the customer
     *
     * @return bool
     */
    public function isPaidByCustomer(): bool
    {
        return ($this->commission_by ?? null) === self::COMMISSION_BY_CUSTOMER;
    }

    /**
     * Get bank name from POS integration
     *
     * @return string|null
     */
    public function getBankName(): ?string
    {
        return $this->company_pos_credential['pos_integration']['integration_name'] ?? null;
    }

    /**
     * Get bank logo URL
     *
     * @return string|null
     */
    public function getBankLogoUrl(): ?string
    {
        return $this->company_pos_credential['pos_integration']['bank_logo_url'] ?? null;
    }
}
