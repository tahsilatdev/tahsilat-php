<?php

declare(strict_types=1);

namespace Tahsilat\HttpClient;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\NetworkException;
use Tahsilat\Tahsilat;
use CurlHandle;

/**
 * cURL based HTTP client implementation
 *
 * @package Tahsilat\HttpClient
 */
class CurlClient implements HttpClientInterface
{
    /**
     * @var CurlHandle|resource|null cURL handle (CurlHandle in PHP 8+, resource in PHP 7.x)
     */
    private $curlHandle;

    /**
     * @var array<int, mixed> Default cURL options
     */
    private array $defaultOptions;

    /**
     * @var bool Whether this is a token request
     */
    private bool $isTokenRequest = false;

    /**
     * Constructor
     *
     * @throws NetworkException When cURL extension is not available
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
    private function initializeCurl(): void
    {
        if (!extension_loaded('curl')) {
            throw new NetworkException('cURL extension is not available');
        }

        $handle = curl_init();
        if ($handle === false) {
            throw new NetworkException('Failed to initialize cURL');
        }

        $this->curlHandle = $handle;

        $this->defaultOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CONNECTTIMEOUT => Tahsilat::getConnectTimeout(),
            CURLOPT_TIMEOUT => Tahsilat::getTimeout(),
            CURLOPT_HTTPHEADER => [],
            CURLOPT_FAILONERROR => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // Security: Disable following redirects to prevent SSRF
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
        ];

        // SSL options - always verify in production
        if (Tahsilat::getVerifySslCerts()) {
            $this->defaultOptions[CURLOPT_SSL_VERIFYPEER] = true;
            $this->defaultOptions[CURLOPT_SSL_VERIFYHOST] = 2;

            $caBundlePath = Tahsilat::getCaBundlePath();
            if ($caBundlePath !== null) {
                $this->defaultOptions[CURLOPT_CAINFO] = $caBundlePath;
            }
        } else {
            // Warning: Only disable SSL verification in development environments
            $this->defaultOptions[CURLOPT_SSL_VERIFYPEER] = false;
            $this->defaultOptions[CURLOPT_SSL_VERIFYHOST] = 0;
        }

        // Set user agent
        $curlVersion = curl_version();
        $curlVersionString = is_array($curlVersion) ? ($curlVersion['version'] ?? 'unknown') : 'unknown';

        $this->defaultOptions[CURLOPT_USERAGENT] = sprintf(
            'Tahsilat-PHP/%s (PHP %s; cURL %s)',
            Tahsilat::VERSION,
            PHP_VERSION,
            $curlVersionString
        );
    }

    /**
     * Set whether this is a token request
     *
     * @param bool $isTokenRequest Whether this is a token request
     * @return void
     */
    public function setIsTokenRequest(bool $isTokenRequest): void
    {
        $this->isTokenRequest = $isTokenRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $url, array $headers = [], array $params = []): array
    {
        if ($this->curlHandle === null) {
            $this->initializeCurl();
        }

        $opts = $this->defaultOptions;
        $opts[CURLOPT_URL] = $url;
        $opts[CURLOPT_CUSTOMREQUEST] = strtoupper($method);

        // Check if this is a token request
        $this->isTokenRequest = strpos($url, '/token/get-token') !== false;

        // Merge headers
        $headers = array_merge($this->getDefaultHeaders(), $headers);
        $opts[CURLOPT_HTTPHEADER] = $this->formatHeaders($headers);

        // Handle parameters based on method
        $upperMethod = strtoupper($method);
        if (($upperMethod === 'GET' || $upperMethod === 'DELETE') && !empty($params)) {
            $opts[CURLOPT_URL] .= '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        } elseif (!empty($params)) {
            $contentType = $headers['Content-Type'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $jsonData = json_encode($params, JSON_THROW_ON_ERROR);
                $opts[CURLOPT_POSTFIELDS] = $jsonData;
            } else {
                $opts[CURLOPT_POSTFIELDS] = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            }
        }

        // Apply options to cURL handle
        curl_setopt_array($this->curlHandle, $opts);

        // Execute request with retries
        $retries = 0;
        $maxRetries = Tahsilat::getMaxRetries();
        $response = false;

        while ($retries <= $maxRetries) {
            $response = curl_exec($this->curlHandle);

            if ($response === false) {
                $errno = curl_errno($this->curlHandle);
                $error = curl_error($this->curlHandle);

                if ($retries < $maxRetries && $this->shouldRetry($errno)) {
                    $retries++;
                    // Exponential backoff with jitter (max 2 seconds)
                    $sleepTime = min($retries * 500000 + random_int(0, 100000), 2000000);
                    usleep($sleepTime);
                    continue;
                }

                throw new NetworkException("cURL error {$errno}: {$error}");
            }

            break;
        }

        if (!is_string($response)) {
            throw new NetworkException('Invalid response from server');
        }

        // Parse response
        $headerSize = (int) curl_getinfo($this->curlHandle, CURLINFO_HEADER_SIZE);
        $httpCode = (int) curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        $responseBody = substr($response, $headerSize);

        // Handle empty response
        if (empty($responseBody)) {
            throw new ApiErrorException('Empty response from API', $httpCode);
        }

        // Parse response body
        $data = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiErrorException(
                sprintf('Invalid JSON response: %s. Response: %s', json_last_error_msg(), substr($responseBody, 0, 500)),
                $httpCode
            );
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
    private function getDefaultHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Accept-Language' => 'tr',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        // For token requests, always use API key
        // For other requests, use access token if available, otherwise use API key
        if ($this->isTokenRequest) {
            $apiKey = Tahsilat::getApiKey();
            if ($apiKey !== null) {
                $headers['Authorization'] = 'Bearer ' . $apiKey;
            }
        } else {
            $accessToken = Tahsilat::getAccessToken();
            $apiKey = Tahsilat::getApiKey();

            if ($accessToken !== null) {
                $headers['Authorization'] = 'Bearer ' . $accessToken;
            } elseif ($apiKey !== null) {
                $headers['Authorization'] = 'Bearer ' . $apiKey;
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
    private function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            // Sanitize header values to prevent header injection
            $key = str_replace(["\r", "\n"], '', (string) $key);
            $value = str_replace(["\r", "\n"], '', (string) $value);
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
    private function shouldRetry(int $errno): bool
    {
        $retriableErrors = [
            CURLE_COULDNT_CONNECT,
            CURLE_COULDNT_RESOLVE_HOST,
            CURLE_OPERATION_TIMEOUTED,
            CURLE_SSL_CONNECT_ERROR,
        ];

        return in_array($errno, $retriableErrors, true);
    }

    /**
     * Handle API error response
     *
     * @param array<string, mixed> $data Response data
     * @param int $httpCode HTTP status code
     * @return never
     * @throws ApiErrorException
     */
    private function handleApiError(array $data, int $httpCode): void
    {
        $message = $data['message'] ?? 'Unknown error occurred';
        $errorCode = $data['error_code'] ?? null;

        // If there are validation errors, append them to the message
        if (isset($data['errors']) && is_array($data['errors']) && !empty($data['errors'])) {
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
        $exceptionCode = $errorCode !== null ? (int) $errorCode : $httpCode;

        throw new ApiErrorException($message, $exceptionCode, $errorCode, $data);
    }

    /**
     * Destructor - cleanup curl handle
     *
     * Note: curl_close() is deprecated since PHP 8.0 (no-op) and formally deprecated in 8.5
     * The handle is automatically cleaned up by PHP's garbage collector
     */
    public function __destruct()
    {
        // PHP 8.0+: curl_close() has no effect, handle is auto-cleaned by GC
        // PHP 7.x: we need to explicitly close the handle
        if (PHP_VERSION_ID < 80000 && $this->curlHandle !== null) {
            if (is_resource($this->curlHandle) && get_resource_type($this->curlHandle) === 'curl') {
                curl_close($this->curlHandle);
            }
        }

        $this->curlHandle = null;
    }
}