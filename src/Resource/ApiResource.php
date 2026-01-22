<?php

declare(strict_types=1);

namespace Tahsilat\Resource;

use JsonSerializable;

/**
 * Base class for all API resources
 *
 * PHP 8.2+ requires explicit declaration for dynamic properties.
 * This class uses the AllowDynamicProperties attribute when available.
 *
 * @package Tahsilat\Resource
 */
#[\AllowDynamicProperties]
abstract class ApiResource implements JsonSerializable
{
    /**
     * @var array<string, mixed> Internal data storage
     */
    protected array $_data = [];

    /**
     * Constructor
     *
     * @param array<string, mixed> $data Resource data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->_data[$key] = $value;
            $this->{$key} = $value;
        }
    }

    /**
     * Get all data as array (includes null and false values)
     *
     * @return array<string, mixed> Resource data
     */
    public function toArray(): array
    {
        return $this->_data;
    }

    /**
     * JsonSerializable implementation
     *
     * @return array<string, mixed>
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert resource to JSON
     *
     * @param int $options JSON encode options
     * @return string JSON representation
     */
    public function toJson(int $options = 0): string
    {
        $json = json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
        return $json !== false ? $json : '{}';
    }

    /**
     * String representation of resource
     *
     * @return string String representation
     */
    public function __toString(): string
    {
        try {
            return $this->toJson(JSON_PRETTY_PRINT);
        } catch (\JsonException $e) {
            return '{}';
        }
    }

    /**
     * Get a value from the resource
     *
     * @param string $key The key to get
     * @param mixed $default Default value if key doesn't exist
     * @return mixed The value
     */
    public function get(string $key, $default = null)
    {
        return array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
    }

    /**
     * Set a value in the resource
     *
     * @param string $key The key to set
     * @param mixed $value The value to set
     * @return static
     */
    public function set(string $key, $value): self
    {
        $this->_data[$key] = $value;
        $this->{$key} = $value;
        return $this;
    }

    /**
     * Check if a key exists in the resource
     *
     * @param string $key The key to check
     * @return bool Whether the key exists
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Check if a value is null
     *
     * @param string $key The key to check
     * @return bool Whether the value is null
     */
    public function isNull(string $key): bool
    {
        return array_key_exists($key, $this->_data) && $this->_data[$key] === null;
    }

    /**
     * Create a new instance from array
     *
     * @param array<string, mixed> $data Data array
     * @return static New instance
     */
    public static function fromArray(array $data): self
    {
        return new static($data);
    }

    /**
     * Debug info for var_dump
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return $this->_data;
    }
}
