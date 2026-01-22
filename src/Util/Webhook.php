<?php

declare(strict_types=1);

namespace Tahsilat\Util;

use Tahsilat\Exception\SignatureVerificationException;
use Tahsilat\Resource\WebhookEvent;

/**
 * Webhook signature verification and event construction
 *
 * @package Tahsilat\Util
 */
final class Webhook
{
    /**
     * Default tolerance in seconds for timestamp verification
     */
    private const DEFAULT_TOLERANCE = 300;

    /**
     * Verify webhook signature and construct event
     *
     * @param string $payload Raw request body
     * @param string $sigHeader Tahsilat-Signature header value
     * @param string $endpointSecret Expected webhook signature/secret
     * @param int $tolerance Maximum age of the event in seconds (default: 300)
     * @return WebhookEvent Verified webhook event
     * @throws SignatureVerificationException When signature verification fails
     */
    public static function constructEvent(
        string $payload,
        string $sigHeader,
        string $endpointSecret,
        int $tolerance = self::DEFAULT_TOLERANCE
    ): WebhookEvent {
        // Validate inputs
        if (empty($endpointSecret)) {
            throw new SignatureVerificationException('Webhook endpoint secret is required');
        }

        if (empty($sigHeader)) {
            throw new SignatureVerificationException('No Tahsilat-Signature header found');
        }

        if (empty($payload)) {
            throw new SignatureVerificationException('Webhook payload is empty');
        }

        // Use timing-safe comparison to prevent timing attacks
        if (!self::secureCompare($sigHeader, $endpointSecret)) {
            throw new SignatureVerificationException('Invalid webhook signature');
        }

        // Parse and validate JSON payload
        $data = json_decode($payload, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SignatureVerificationException(
                'Invalid JSON payload: ' . json_last_error_msg()
            );
        }

        if (!is_array($data)) {
            throw new SignatureVerificationException('Webhook payload must be a JSON object');
        }

        return new WebhookEvent($data);
    }

    /**
     * Verify webhook signature using HMAC (for future HMAC-based signature verification)
     *
     * @param string $payload Raw request body
     * @param string $sigHeader Signature header value
     * @param string $secret Webhook secret
     * @param string $algorithm Hash algorithm (default: sha256)
     * @return bool Whether signature is valid
     */
    public static function verifyHmacSignature(
        string $payload,
        string $sigHeader,
        string $secret,
        string $algorithm = 'sha256'
    ): bool {
        $expectedSignature = hash_hmac($algorithm, $payload, $secret);
        
        return self::secureCompare($sigHeader, $expectedSignature);
    }

    /**
     * Timing-safe string comparison
     *
     * This function compares two strings in constant time to prevent
     * timing attacks that could be used to guess the signature.
     *
     * @param string $a First string
     * @param string $b Second string
     * @return bool Whether strings are equal
     */
    private static function secureCompare(string $a, string $b): bool
    {
        // hash_equals is available since PHP 5.6.0 and is timing-safe
        return hash_equals($a, $b);
    }

    /**
     * Parse signature header with timestamp (for future timestamped signatures)
     *
     * Expected format: t=timestamp,v1=signature
     *
     * @param string $header Signature header
     * @return array{timestamp: int|null, signature: string|null} Parsed components
     */
    public static function parseSignatureHeader(string $header): array
    {
        $result = [
            'timestamp' => null,
            'signature' => null,
        ];

        $parts = explode(',', $header);
        
        foreach ($parts as $part) {
            $keyValue = explode('=', $part, 2);
            
            if (count($keyValue) !== 2) {
                continue;
            }

            [$key, $value] = $keyValue;
            $key = trim($key);
            $value = trim($value);

            if ($key === 't') {
                $result['timestamp'] = (int) $value;
            } elseif ($key === 'v1') {
                $result['signature'] = $value;
            }
        }

        return $result;
    }

    /**
     * Prevent instantiation
     */
    private function __construct()
    {
    }
}
