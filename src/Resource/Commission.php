<?php

namespace Tahsilat\Resource;

/**
 * Commission resource
 *
 * @property int $id Commission ID
 * @property int $company_pos_credential_id POS credential ID
 * @property int $merchant_id Merchant ID
 * @property int $installment Installment count
 * @property float $commission_rate Commission rate percentage
 * @property int $commission_by Commission payer (1: Merchant, 2: Customer)
 * @property string $commission_by_text Commission payer text ("Üye İşyeri" or "Müşteri")
 * @property string $created_at Creation timestamp
 * @property string $updated_at Update timestamp
 * @property array $company_pos_credential POS credential details
 *
 * @package Tahsilat\Resource
 */
class Commission extends ApiResource
{
    /**
     * Commission payer constants
     */
    const COMMISSION_BY_MERCHANT = 1;
    const COMMISSION_BY_CUSTOMER = 2;

    /**
     * Check if commission is paid by merchant
     *
     * @return bool
     */
    public function isPaidByMerchant()
    {
        return $this->commission_by === self::COMMISSION_BY_MERCHANT;
    }

    /**
     * Check if commission is paid by customer
     *
     * @return bool
     */
    public function isPaidByCustomer()
    {
        return $this->commission_by === self::COMMISSION_BY_CUSTOMER;
    }

    /**
     * Get bank name from POS integration
     *
     * @return string|null
     */
    public function getBankName()
    {
        return isset($this->company_pos_credential['pos_integration']['integration_name'])
            ? $this->company_pos_credential['pos_integration']['integration_name']
            : null;
    }

    /**
     * Get bank logo URL
     *
     * @return string|null
     */
    public function getBankLogoUrl()
    {
        return isset($this->company_pos_credential['pos_integration']['bank_logo_url'])
            ? $this->company_pos_credential['pos_integration']['bank_logo_url']
            : null;
    }
}