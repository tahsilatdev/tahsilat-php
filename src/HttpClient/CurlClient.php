<?php

namespace Tahsilat\HttpClient;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\NetworkException;
use Tahsilat\Tahsilat;

/**
 * cURL based HTTP client implementation
 *
 * @package Tahsilat\HttpClient
 */
class CurlClient implements HttpClientInterface
{
    /**
     * @var resource|false cURL handle
     */
    private $curlHandle;

    /**
     * @var array<string, mixed> Default cURL options
     */
    private $defaultOptions;

    /**
     * @var bool Whether this is a token request
     */
    private $isTokenRequest = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initializeCurl();
    }

    /**
     * Initialize cURL handle and set default options
     *
     * @return void
     * @throws NetworkException When cURL extension is not available
     */
    private function initializeCurl()
    {
        if (!extension_loaded('curl')) {
            throw new NetworkException('cURL extension is not available');
        }

        $this->curlHandle = curl_init();
        if ($this->curlHandle === false) {
            throw new NetworkException('Failed to initialize cURL');
        }

        $this->defaultOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CONNECTTIMEOUT => Tahsilat::getConnectTimeout(),
            CURLOPT_TIMEOUT => Tahsilat::getTimeout(),
            CURLOPT_HTTPHEADER => [],
            CURLOPT_FAILONERROR => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ];

        // SSL options - always verify in production
        if (Tahsilat::getVerifySslCerts()) {
            $this->defaultOptions[CURLOPT_SSL_VERIFYPEER] = true;
            $this->defaultOptions[CURLOPT_SSL_VERIFYHOST] = 2;

            if (Tahsilat::getCaBundlePath()) {
                $this->defaultOptions[CURLOPT_CAINFO] = Tahsilat::getCaBundlePath();
            }
        } else {
            $this->defaultOptions[CURLOPT_SSL_VERIFYPEER] = false;
            $this->defaultOptions[CURLOPT_SSL_VERIFYHOST] = 0;
        }

        // Set user agent
        $phpVersion = PHP_VERSION;
        $curlVersion = curl_version();
        $this->defaultOptions[CURLOPT_USERAGENT] = 'Tahsilat-PHP/' . TAHSILAT_VERSION .
            ' (PHP ' . $phpVersion . '; cURL ' . $curlVersion['version'] . ')';
    }

    /**
     * Set whether this is a token request
     *
     * @param bool $isTokenRequest Whether this is a token request
     * @return void
     */
    public function setIsTokenRequest($isTokenRequest)
    {
        $this->isTokenRequest = $isTokenRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function request($method, $url, $headers = [], $params = [])
    {
        $opts = $this->defaultOptions;
        $opts[CURLOPT_URL] = $url;
        $opts[CURLOPT_CUSTOMREQUEST] = strtoupper($method);

        // Check if this is a token request
        $this->isTokenRequest = strpos($url, '/token/get-token') !== false;

        // Merge headers
        $headers = array_merge($this->getDefaultHeaders(), $headers);
        $opts[CURLOPT_HTTPHEADER] = $this->formatHeaders($headers);

        // Handle parameters based on method
        if (strtoupper($method) === 'GET' && !empty($params)) {
            $opts[CURLOPT_URL] .= '?' . http_build_query($params);
        } elseif (strtoupper($method) === 'DELETE' && !empty($params)) {
            $opts[CURLOPT_URL] .= '?' . http_build_query($params);
        } elseif (!empty($params)) {
            if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'application/json') !== false) {
                $opts[CURLOPT_POSTFIELDS] = json_encode($params);
            } else {
                $opts[CURLOPT_POSTFIELDS] = http_build_query($params);
            }
        }

        // Apply options to cURL handle
        curl_setopt_array($this->curlHandle, $opts);

        // Execute request with retries
        $retries = 0;
        $maxRetries = Tahsilat::getMaxRetries();

        while ($retries <= $maxRetries) {
            $response = curl_exec($this->curlHandle);

            if ($response === false) {
                $errno = curl_errno($this->curlHandle);
                $error = curl_error($this->curlHandle);

                if ($retries < $maxRetries && $this->shouldRetry($errno)) {
                    $retries++;
                    usleep(min($retries * 500000, 2000000)); // Exponential backoff
                    continue;
                }

                throw new NetworkException("cURL error {$errno}: {$error}");
            }

            break;
        }

        // Parse response
        $headerSize = curl_getinfo($this->curlHandle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        $responseHeaders = substr($response, 0, $headerSize);
        $responseBody = substr($response, $headerSize);

        // Handle empty response
        if (empty($responseBody)) {
            throw new ApiErrorException("Empty response from API", $httpCode);
        }

        // Parse response body
        $data = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiErrorException("Invalid JSON response: " . json_last_error_msg() . ". Response: " . $responseBody, $httpCode);
        }

        // Check for API errors
        if (isset($data['status']) && $data['status'] === false) {
            $this->handleApiError($data, $httpCode);
        } elseif ($httpCode >= 400) {
            // If HTTP error but no status field, still handle as error
            $this->handleApiError($data, $httpCode);
        }

        return $data;
    }

    /**
     * Get default headers
     *
     * @return array<string, string> Default headers
     */
    private function getDefaultHeaders()
    {
        $headers = [
            'Accept' => 'application/json',
            'Accept-Language' => 'tr',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        // For token requests, always use API key
        // For other requests, use access token if available, otherwise use API key
        if ($this->isTokenRequest) {
            if (Tahsilat::getApiKey()) {
                $headers['Authorization'] = 'Bearer ' . Tahsilat::getApiKey();
            }
        } else {
            if (Tahsilat::getAccessToken()) {
                $headers['Authorization'] = 'Bearer ' . Tahsilat::getAccessToken();
            } elseif (Tahsilat::getApiKey()) {
                $headers['Authorization'] = 'Bearer ' . Tahsilat::getApiKey();
            }
        }

        return $headers;
    }

    /**
     * Format headers for cURL
     *
     * @param array<string, string> $headers Headers array
     * @return array<int, string> Formatted headers
     */
    private function formatHeaders($headers)
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = $key . ': ' . $value;
        }
        return $formatted;
    }

    /**
     * Determine if request should be retried
     *
     * @param int $errno cURL error number
     * @return bool Whether to retry
     */
    private function shouldRetry($errno)
    {
        $retriableErrors = [
            CURLE_COULDNT_CONNECT,
            CURLE_COULDNT_RESOLVE_HOST,
            CURLE_OPERATION_TIMEOUTED,
            CURLE_SSL_CONNECT_ERROR,
        ];

        return in_array($errno, $retriableErrors);
    }

    /**
     * Handle API error response
     *
     * @param array<string, mixed> $data Response data
     * @param int $httpCode HTTP status code
     * @return void
     * @throws ApiErrorException
     */
    private function handleApiError($data, $httpCode)
    {
        $message = isset($data['message']) ? $data['message'] : 'Unknown error occurred';
        $errorCode = isset($data['error_code']) ? $data['error_code'] : null;

        // If there are validation errors, append them to the message
        if (isset($data['errors']) && !empty($data['errors']) && is_array($data['errors'])) {
            $validationErrors = [];
            foreach ($data['errors'] as $field => $messages) {
                if (is_array($messages)) {
                    foreach ($messages as $msg) {
                        $validationErrors[] = $field . ': ' . $msg;
                    }
                } else {
                    $validationErrors[] = $field . ': ' . $messages;
                }
            }

            if (!empty($validationErrors)) {
                $message .= ' ' . implode(' ', $validationErrors);
            }
        }

        // Use error_code as exception code if available, otherwise use HTTP status code
        $exceptionCode = $errorCode !== null ? $errorCode : $httpCode;

        throw new ApiErrorException($message, $exceptionCode, $errorCode, $data);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (is_resource($this->curlHandle)) {
            curl_close($this->curlHandle);
        }
    }
}