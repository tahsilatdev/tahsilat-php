<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

/**
 * Product resource
 *
 * @property int $id Product ID
 * @property int $merchant_id Merchant ID
 * @property string $product_name Product name
 * @property int $price Product price (in kuruş/cents)
 * @property int|null $currency_id Currency ID
 * @property string $currency_code Currency code (TRY, USD, EUR)
 * @property int $quantity Product quantity
 * @property string $description Product description
 * @property string|null $product_image Product image path
 * @property string|null $product_image_url Full product image URL
 * @property string $created_at Creation timestamp (ISO 8601)
 * @property string $updated_at Update timestamp (ISO 8601)
 * @property string $formatted_price Human-readable price (e.g., "99,99")
 * @property string $formatted_created_at Human-readable creation date
 * @property int $system_id System ID
 * @property array $metadata Product metadata array
 * @property bool $is_recurring Whether the product is a subscription (recurring) product
 * @property int|null $billing_interval Billing interval unit (1 day, 2 week, 3 month, 4 year)
 * @property int|null $interval_count Interval multiplier (1-366)
 * @property string|null $recurring_text Human-readable billing interval (e.g., "Aylık", "2 ayda bir")
 * @property string|null $payment_link Payment link if available
 * @property array $timeline Product activity timeline
 *
 * @package Tahsilat\Resource
 */
class Product extends ApiResource
{
    /**
     * Get price in decimal format (e.g., 99.99)
     *
     * @return float
     */
    public function getPriceDecimal(): float
    {
        return ($this->price ?? 0) / 100;
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
     * Get the timeline events
     *
     * @return array
     */
    public function getTimeline(): array
    {
        return $this->timeline ?? [];
    }

    /**
     * Check if the product is a subscription (recurring) product
     *
     * @return bool
     */
    public function isRecurring(): bool
    {
        return (bool) $this->get('is_recurring', false);
    }

    /**
     * Get the human-readable billing interval (e.g., "Aylık", "2 ayda bir")
     *
     * @return string|null
     */
    public function getRecurringText(): ?string
    {
        return $this->get('recurring_text');
    }
}