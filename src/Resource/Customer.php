<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

/**
 * Customer resource
 *
 * @property int $id Customer ID
 * @property int $merchant_id Merchant ID
 * @property string $name Customer first name
 * @property string $lastname Customer last name
 * @property string $name_lastname Customer full name
 * @property string|null $phone_code Phone country code (e.g., +90)
 * @property string|null $phone Customer phone number
 * @property string|null $email Customer email
 * @property string|null $country Customer country code (e.g., TR)
 * @property string|null $country_flag_url Country flag image URL
 * @property string|null $city Customer city
 * @property string|null $district Customer district
 * @property string|null $address Customer address
 * @property string|null $zip_code Customer zip code
 * @property bool|null $created_via_payment Whether customer was created via payment
 * @property string|null $created_at Creation timestamp (ISO 8601)
 * @property string|null $updated_at Update timestamp (ISO 8601)
 * @property string|null $formatted_created_at Human-readable creation date
 * @property array|null $metadata Customer metadata array
 * @property string|null $payment_link Payment link if available
 * @property array|null $timeline Customer activity timeline
 *
 * @package Tahsilat\Resource
 */
class Customer extends ApiResource
{
    /**
     * Get customer's full name
     *
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->name_lastname ?? (
            ($this->name ?? '') . ' ' . ($this->lastname ?? '')
        ) ?: null;
    }

    /**
     * Get customer's full phone number with country code
     *
     * @return string|null
     */
    public function getFullPhone(): ?string
    {
        $phoneCode = $this->phone_code ?? '';
        $phone = $this->phone ?? '';

        if (empty($phone)) {
            return null;
        }

        return trim($phoneCode . ' ' . $phone);
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
     * Check if customer was created via a payment
     *
     * @return bool
     */
    public function wasCreatedViaPayment(): bool
    {
        return !empty($this->created_via_payment);
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