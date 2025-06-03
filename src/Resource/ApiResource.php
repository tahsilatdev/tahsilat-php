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
     * @var array<string, mixed> Resource data
     */
    protected $data;

    /**
     * Constructor
     *
     * @param array<string, mixed> $data Resource data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Get property value
     *
     * @param string $name Property name
     * @return mixed Property value
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Set property value
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if property exists
     *
     * @param string $name Property name
     * @return bool Whether property exists
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Unset property
     *
     * @param string $name Property name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Get all data as array
     *
     * @return array<string, mixed> Resource data
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Convert resource to JSON
     *
     * @param int $options JSON encode options
     * @return string JSON representation
     */
    public function toJson($options = 0)
    {
        return json_encode($this->data, $options);
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
     * Get a value from the resource data
     *
     * @param string $key The key to get
     * @param mixed $default Default value if key doesn't exist
     * @return mixed The value
     */
    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * Set a value in the resource data
     *
     * @param string $key The key to set
     * @param mixed $value The value to set
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Check if a key exists in the resource data
     *
     * @param string $key The key to check
     * @return bool Whether the key exists
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Remove a key from the resource data
     *
     * @param string $key The key to remove
     * @return $this
     */
    public function remove($key)
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * Clear all data
     *
     * @return $this
     */
    public function clear()
    {
        $this->data = [];
        return $this;
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