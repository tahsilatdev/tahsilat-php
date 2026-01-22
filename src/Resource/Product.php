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
 * @property string $currency_code Currency code (TRY, USD, EUR)
 * @property int $quantity Product quantity
 * @property string $description Product description
 * @property string|null $product_image Product image URL
 * @property string $created_at Creation timestamp (ISO 8601)
 * @property string $updated_at Update timestamp (ISO 8601)
 * @property string $formatted_price Human-readable price (e.g., "99,99")
 * @property string $formatted_created_at Human-readable creation date
 * @property int $system_id System ID
 * @property array $metadata Product metadata array
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
}