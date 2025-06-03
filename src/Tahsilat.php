<?php

namespace Tahsilat;

/**
 * Class Tahsilat
 *
 * @package Tahsilat
 */
class Tahsilat
{
    /**
     * @var string The Tahsilat API base URL
     */
    const API_BASE = 'https://api.tahsilat.dev/v1/';

    /**
     * @var string|null The API key to be used for requests
     */
    private static $apiKey;

    /**
     * @var string|null The access token for authenticated requests
     */
    private static $accessToken;

    /**
     * @var string The API version
     */
    private static $apiVersion = 'v1';

    /**
     * @var int Maximum number of request retries
     */
    private static $maxRetries = 3;

    /**
     * @var int Connection timeout in seconds
     */
    private static $connectTimeout = 30;

    /**
     * @var int Request timeout in seconds
     */
    private static $timeout = 80;

    /**
     * @var bool Whether to verify SSL certificates
     */
    private static $verifySslCerts = true;

    /**
     * @var string|null Custom CA bundle path
     */
    private static $caBundlePath = null;

    /**
     * Sets the API key to be used for requests
     *
     * @param string $apiKey The API key
     * @return void
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * Gets the API key used for requests
     *
     * @return string|null The API key
     */
    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * Sets the access token
     *
     * @param string $accessToken The access token
     * @return void
     */
    public static function setAccessToken($accessToken)
    {
        self::$accessToken = $accessToken;
    }

    /**
     * Gets the access token
     *
     * @return string|null The access token
     */
    public static function getAccessToken()
    {
        return self::$accessToken;
    }

    /**
     * Sets the API version
     *
     * @param string $apiVersion The API version
     * @return void
     */
    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }

    /**
     * Gets the API version
     *
     * @return string The API version
     */
    public static function getApiVersion()
    {
        return self::$apiVersion;
    }

    /**
     * Sets the maximum number of request retries
     *
     * @param int $maxRetries The maximum number of retries
     * @return void
     */
    public static function setMaxRetries($maxRetries)
    {
        self::$maxRetries = $maxRetries;
    }

    /**
     * Gets the maximum number of request retries
     *
     * @return int The maximum number of retries
     */
    public static function getMaxRetries()
    {
        return self::$maxRetries;
    }

    /**
     * Sets the connection timeout
     *
     * @param int $seconds The connection timeout in seconds
     * @return void
     */
    public static function setConnectTimeout($seconds)
    {
        self::$connectTimeout = $seconds;
    }

    /**
     * Gets the connection timeout
     *
     * @return int The connection timeout in seconds
     */
    public static function getConnectTimeout()
    {
        return self::$connectTimeout;
    }

    /**
     * Sets the request timeout
     *
     * @param int $seconds The request timeout in seconds
     * @return void
     */
    public static function setTimeout($seconds)
    {
        self::$timeout = $seconds;
    }

    /**
     * Gets the request timeout
     *
     * @return int The request timeout in seconds
     */
    public static function getTimeout()
    {
        return self::$timeout;
    }

    /**
     * Sets whether to verify SSL certificates
     *
     * @param bool $verify Whether to verify SSL certificates
     * @return void
     */
    public static function setVerifySslCerts($verify)
    {
        self::$verifySslCerts = $verify;
    }

    /**
     * Gets whether SSL certificates are being verified
     *
     * @return bool Whether SSL certificates are being verified
     */
    public static function getVerifySslCerts()
    {
        return self::$verifySslCerts;
    }

    /**
     * Sets the CA bundle path
     *
     * @param string|null $path The CA bundle path
     * @return void
     */
    public static function setCaBundlePath($path)
    {
        self::$caBundlePath = $path;
    }

    /**
     * Gets the CA bundle path
     *
     * @return string|null The CA bundle path
     */
    public static function getCaBundlePath()
    {
        return self::$caBundlePath;
    }

    /**
     * Gets the API base URL
     *
     * @return string The API base URL
     */
    public static function getApiBase()
    {
        return self::API_BASE;
    }
}