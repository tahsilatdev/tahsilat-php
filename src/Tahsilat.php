<?php

declare(strict_types=1);

namespace Tahsilat;

/**
 * Class Tahsilat
 *
 * Main configuration class for the Tahsilat SDK
 *
 * @package Tahsilat
 */
class Tahsilat
{
    /**
     * @var string SDK Version
     */
    public const VERSION = '2.0.0';

    /**
     * @var string Minimum required PHP version
     */
    public const MIN_PHP_VERSION = '7.2.0';

    /**
     * @var string The Tahsilat API base URL for live environment
     */
    public const API_LIVE_BASE = 'https://api.tahsilat.com/v1/';

    /**
     * @var string The Tahsilat API base URL for sandbox environment
     */
    public const API_SANDBOX_BASE = 'https://api.sandbox.tahsilat.com/v1/';

    /**
     * @var string|null The API key to be used for requests
     */
    private static ?string $apiKey = null;

    /**
     * @var string|null The access token for authenticated requests
     */
    private static ?string $accessToken = null;

    /**
     * @var string The API version
     */
    private static string $apiVersion = 'v1';

    /**
     * @var int Maximum number of request retries
     */
    private static int $maxRetries = 3;

    /**
     * @var int Connection timeout in seconds
     */
    private static int $connectTimeout = 30;

    /**
     * @var int Request timeout in seconds
     */
    private static int $timeout = 80;

    /**
     * @var bool Whether to verify SSL certificates
     */
    private static bool $verifySslCerts = true;

    /**
     * @var string|null Custom CA bundle path
     */
    private static ?string $caBundlePath = null;

    /**
     * Sets the API key to be used for requests
     *
     * @param string $apiKey The API key
     * @return void
     */
    public static function setApiKey(string $apiKey): void
    {
        self::$apiKey = $apiKey;
    }

    /**
     * Gets the API key used for requests
     *
     * @return string|null The API key
     */
    public static function getApiKey(): ?string
    {
        return self::$apiKey;
    }

    /**
     * Sets the access token
     *
     * @param string $accessToken The access token
     * @return void
     */
    public static function setAccessToken(string $accessToken): void
    {
        self::$accessToken = $accessToken;
    }

    /**
     * Gets the access token
     *
     * @return string|null The access token
     */
    public static function getAccessToken(): ?string
    {
        return self::$accessToken;
    }

    /**
     * Sets the API version
     *
     * @param string $apiVersion The API version
     * @return void
     */
    public static function setApiVersion(string $apiVersion): void
    {
        self::$apiVersion = $apiVersion;
    }

    /**
     * Gets the API version
     *
     * @return string The API version
     */
    public static function getApiVersion(): string
    {
        return self::$apiVersion;
    }

    /**
     * Sets the maximum number of request retries
     *
     * @param int $maxRetries The maximum number of retries
     * @return void
     */
    public static function setMaxRetries(int $maxRetries): void
    {
        self::$maxRetries = max(0, $maxRetries);
    }

    /**
     * Gets the maximum number of request retries
     *
     * @return int The maximum number of retries
     */
    public static function getMaxRetries(): int
    {
        return self::$maxRetries;
    }

    /**
     * Sets the connection timeout
     *
     * @param int $seconds The connection timeout in seconds
     * @return void
     */
    public static function setConnectTimeout(int $seconds): void
    {
        self::$connectTimeout = max(1, $seconds);
    }

    /**
     * Gets the connection timeout
     *
     * @return int The connection timeout in seconds
     */
    public static function getConnectTimeout(): int
    {
        return self::$connectTimeout;
    }

    /**
     * Sets the request timeout
     *
     * @param int $seconds The request timeout in seconds
     * @return void
     */
    public static function setTimeout(int $seconds): void
    {
        self::$timeout = max(1, $seconds);
    }

    /**
     * Gets the request timeout
     *
     * @return int The request timeout in seconds
     */
    public static function getTimeout(): int
    {
        return self::$timeout;
    }

    /**
     * Sets whether to verify SSL certificates
     *
     * @param bool $verify Whether to verify SSL certificates
     * @return void
     */
    public static function setVerifySslCerts(bool $verify): void
    {
        self::$verifySslCerts = $verify;
    }

    /**
     * Gets whether SSL certificates are being verified
     *
     * @return bool Whether SSL certificates are being verified
     */
    public static function getVerifySslCerts(): bool
    {
        return self::$verifySslCerts;
    }

    /**
     * Sets the CA bundle path
     *
     * @param string|null $path The CA bundle path
     * @return void
     */
    public static function setCaBundlePath(?string $path): void
    {
        self::$caBundlePath = $path;
    }

    /**
     * Gets the CA bundle path
     *
     * @return string|null The CA bundle path
     */
    public static function getCaBundlePath(): ?string
    {
        return self::$caBundlePath;
    }

    /**
     * Gets the API base URL based on the API key
     *
     * If an API key contains 'test', returns sandbox URL
     * Otherwise returns live URL
     *
     * @return string The API base URL
     */
    public static function getApiBase(): string
    {
        $apiKey = self::getApiKey();

        // If no API key is set, default to live (this will likely cause an auth error later)
        if ($apiKey === null) {
            return self::API_LIVE_BASE;
        }

        // Check if the API key contains 'test' to determine the environment
        if (strpos($apiKey, 'test') !== false) {
            return self::API_SANDBOX_BASE;
        }

        return self::API_LIVE_BASE;
    }

    /**
     * Check if currently using sandbox environment
     *
     * @return bool True if using sandbox, false if using live
     */
    public static function isSandbox(): bool
    {
        return self::getApiBase() === self::API_SANDBOX_BASE;
    }

    /**
     * Check if currently using a live environment
     *
     * @return bool True if using live, false if using sandbox
     */
    public static function isLive(): bool
    {
        return self::getApiBase() === self::API_LIVE_BASE;
    }

    /**
     * Reset all static properties to their default values
     * Useful for testing
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$apiKey = null;
        self::$accessToken = null;
        self::$apiVersion = 'v1';
        self::$maxRetries = 3;
        self::$connectTimeout = 30;
        self::$timeout = 80;
        self::$verifySslCerts = true;
        self::$caBundlePath = null;
    }
}
