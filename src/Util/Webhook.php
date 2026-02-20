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
     * @param string $sigHeader X-Tahsilat-Signature header value (format: t=timestamp,v1=signature)
     * @param string $endpointSecret Webhook signing secret
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
        if (empty($endpointSecret)) {
            throw new SignatureVerificationException('Webhook endpoint secret is required');
        }

        if (empty($sigHeader)) {
            throw new SignatureVerificationException('No X-Tahsilat-Signature header found');
        }

        if (empty($payload)) {
            throw new SignatureVerificationException('Webhook payload is empty');
        }

        $parsed = self::parseSignatureHeader($sigHeader);

        if ($parsed['timestamp'] === null || $parsed['signature'] === null) {
            throw new SignatureVerificationException(
                'Unable to parse X-Tahsilat-Signature header. Expected format: t=timestamp,v1=signature'
            );
        }

        // Timestamp tolerance check
        $currentTime = time();
        if (abs($currentTime - $parsed['timestamp']) > $tolerance) {
            throw new SignatureVerificationException(
                'Webhook timestamp is outside the tolerance zone. '
                . 'Current time: ' . $currentTime . ', '
                . 'Webhook time: ' . $parsed['timestamp'] . ', '
                . 'Tolerance: ' . $tolerance . 's'
            );
        }

        // Reconstruct expected signature: HMAC-SHA256(timestamp.payload, secret)
        $signedPayload = $parsed['timestamp'] . '.' . $payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $endpointSecret);

        if (!self::secureCompare($parsed['signature'], $expectedSignature)) {
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
     * Timing-safe string comparison
     *
     * @param string $a First string
     * @param string $b Second string
     * @return bool Whether strings are equal
     */
    private static function secureCompare(string $a, string $b): bool
    {
        return hash_equals($a, $b);
    }

    /**
     * Parse signature header with timestamp
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