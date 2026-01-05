<?php

namespace Tahsilat\Resource;

/**
 * Base class for all API resources
 *
 * @package Tahsilat\Resource
 */
if (\PHP_VERSION_ID >= 80200) {
    #[\AllowDynamicProperties]
    abstract class ApiResource implements \JsonSerializable
    {
        /**
         * @var array Internal data storage
         */
        protected $_data = array();

        /**
         * Constructor
         *
         * @param array $data Resource data
         */
        public function __construct($data = array())
        {
            foreach ($data as $key => $value) {
                $this->_data[$key] = $value;
                $this->{$key} = $value;
            }
        }

        /**
         * Get all data as array (includes null and false values)
         *
         * @return array Resource data
         */
        public function toArray()
        {
            return $this->_data;
        }

        /**
         * JsonSerializable implementation
         *
         * @return array
         */
        #[\ReturnTypeWillChange]
        public function jsonSerialize()
        {
            return $this->toArray();
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
            return $this->toJson(JSON_PRETTY_PRINT);
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
            return array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
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
        public function has($key)
        {
            return array_key_exists($key, $this->_data);
        }

        /**
         * Check if a value is null
         *
         * @param string $key The key to check
         * @return bool Whether the value is null
         */
        public function isNull($key)
        {
            return array_key_exists($key, $this->_data) && $this->_data[$key] === null;
        }

        /**
         * Create a new instance from array
         *
         * @param array $data Data array
         * @return static New instance
         */
        public static function fromArray($data)
        {
            return new static($data);
        }

        /**
         * Debug info for var_dump
         *
         * @return array
         */
        public function __debugInfo()
        {
            return $this->_data;
        }
    }
} else {
    abstract class ApiResource implements \JsonSerializable
    {
        /**
         * @var array Internal data storage
         */
        protected $_data = array();

        /**
         * Constructor
         *
         * @param array $data Resource data
         */
        public function __construct($data = array())
        {
            foreach ($data as $key => $value) {
                $this->_data[$key] = $value;
                $this->{$key} = $value;
            }
        }

        /**
         * Get all data as array (includes null and false values)
         *
         * @return array Resource data
         */
        public function toArray()
        {
            return $this->_data;
        }

        /**
         * JsonSerializable implementation
         *
         * @return array
         */
        public function jsonSerialize()
        {
            return $this->toArray();
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
            return $this->toJson(JSON_PRETTY_PRINT);
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
            return array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
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
        public function has($key)
        {
            return array_key_exists($key, $this->_data);
        }

        /**
         * Check if a value is null
         *
         * @param string $key The key to check
         * @return bool Whether the value is null
         */
        public function isNull($key)
        {
            return array_key_exists($key, $this->_data) && $this->_data[$key] === null;
        }

        /**
         * Create a new instance from array
         *
         * @param array $data Data array
         * @return static New instance
         */
        public static function fromArray($data)
        {
            return new static($data);
        }

        /**
         * Debug info for var_dump
         *
         * @return array
         */
        public function __debugInfo()
        {
            return $this->_data;
        }
    }
}