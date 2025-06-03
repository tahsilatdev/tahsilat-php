<?php

namespace Tahsilat\Util;

/**
 * Request options utility class
 *
 * @package Tahsilat\Util
 */
class RequestOptions
{
    /**
     * Normalize request options
     *
     * @param array<string, mixed>|string|null $options Options to normalize
     * @return array<string, mixed> Normalized options
     */
    public static function normalize($options)
    {
        if (is_null($options)) {
            return [];
        }

        if (is_string($options)) {
            return ['api_key' => $options];
        }

        if (!is_array($options)) {
            throw new \InvalidArgumentException('Options must be an array, string, or null');
        }

        return $options;
    }
}