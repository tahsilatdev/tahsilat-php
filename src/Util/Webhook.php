<?php

namespace Tahsilat\Util;

use Tahsilat\Exception\SignatureVerificationException;
use Tahsilat\Resource\WebhookEvent;

/**
 * Webhook signature verification and event construction
 *
 * @package Tahsilat\Util
 */
class Webhook
{
    /**
     * Verify webhook signature and construct event
     *
     * @param string $payload Raw request body
     * @param string $sigHeader Tahsilat-Signature header value
     * @param string $endpointSecret Expected webhook signature
     * @return WebhookEvent Verified webhook event
     * @throws SignatureVerificationException When signature verification fails
     */
    public static function constructEvent($payload, $sigHeader, $endpointSecret)
    {
        if (empty($endpointSecret)) {
            throw new SignatureVerificationException('Webhook endpoint secret is required');
        }

        if (empty($sigHeader)) {
            throw new SignatureVerificationException('No Tahsilat-Signature header found');
        }

        // Simple signature comparison
        if ($sigHeader !== $endpointSecret) {
            throw new SignatureVerificationException('Invalid webhook signature');
        }

        // Parse and return event
        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SignatureVerificationException('Invalid JSON payload');
        }

        return new WebhookEvent($data);
    }
}