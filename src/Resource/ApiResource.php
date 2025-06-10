<?php

namespace Tahsilat\Resource;

/**
 * Base class for all API resources
 *
 * @package Tahsilat\Resource
 */
abstract class ApiResource
{
    /**
     * Constructor
     *
     * @param array<string, mixed> $data Resource data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Get all data as array
     *
     * @return array<string, mixed> Resource data
     */
    public function toArray()
    {
        $vars = get_object_vars($this);
        // Remove any internal properties if needed
        return $vars;
    }

    /**
     * Convert resource to JSON
     *
     * @param int $options JSON encode options
     * @return string JSON representation
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * String representation of resource
     *
     * @return string String representation
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Get a value from the resource
     *
     * @param string $key The key to get
     * @param mixed $default Default value if key doesn't exist
     * @return mixed The value
     */
    public function get($key, $default = null)
    {
        return property_exists($this, $key) ? $this->{$key} : $default;
    }

    /**
     * Set a value in the resource
     *
     * @param string $key The key to set
     * @param mixed $value The value to set
     * @return $this
     */
    public function set($key, $value)
    {
        $this->{$key} = $value;
        return $this;
    }

    /**
     * Check if a key exists in the resource
     *
     * @param string $key The key to check
     * @return bool Whether the key exists
     */
    public function has($key)
    {
        return property_exists($this, $key);
    }

    /**
     * Create a new instance from array
     *
     * @param array<string, mixed> $data Data array
     * @return static New instance
     */
    public static function fromArray($data)
    {
        return new static($data);
    }
}