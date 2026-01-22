<?php

declare(strict_types=1);

namespace Tahsilat\Util;

use InvalidArgumentException;

/**
 * Request options utility class
 *
 * @package Tahsilat\Util
 */
final class RequestOptions
{
    /**
     * Normalize request options
     *
     * @param array<string, mixed>|string|null $options Options to normalize
     * @return array<string, mixed> Normalized options
     * @throws InvalidArgumentException When options are invalid
     */
    public static function normalize($options): array
    {
        if ($options === null) {
            return [];
        }

        if (is_string($options)) {
            return ['api_key' => $options];
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException('Options must be an array, string, or null');
        }

        return $options;
    }

    /**
     * Prevent instantiation
     */
    private function __construct()
    {
    }
}
