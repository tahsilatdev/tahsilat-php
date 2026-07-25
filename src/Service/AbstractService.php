<?php

declare(strict_types=1);

namespace Tahsilat\Service;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\HttpClient\CurlClient;
use Tahsilat\HttpClient\HttpClientInterface;
use Tahsilat\TahsilatClient;

/**
 * Abstract base class for all services
 *
 * @package Tahsilat\Service
 */
abstract class AbstractService
{
    /**
     * @var TahsilatClient The client instance
     */
    protected TahsilatClient $client;

    /**
     * @var HttpClientInterface HTTP client instance
     */
    protected HttpClientInterface $httpClient;

    /**
     * Constructor
     *
     * @param TahsilatClient $client The client instance
     * @param HttpClientInterface|null $httpClient Custom HTTP client (tests)
     */
    public function __construct(TahsilatClient $client, ?HttpClientInterface $httpClient = null)
    {
        $this->client = $client;
        $this->httpClient = $httpClient ?? new CurlClient();
    }

    /**
     * Make an API request
     *
     * @param string $method HTTP method
     * @param string $path API path
     * @param array<string, mixed> $params Request parameters
     * @param array<string, mixed> $opts Request options
     * @return array<string, mixed> Response data
     * @throws ApiErrorException When the API returns an error
     */
    protected function request(string $method, string $path, array $params = [], array $opts = []): array
    {
        $isTokenPath = strpos($path, 'token/get-token') !== false;

        // Instance-scoped base URL and Authorization: two clients (e.g. live
        // and sandbox) can coexist in one process without clobbering each
        // other through the global statics.
        $url = $this->client->getApiBase() . ltrim($path, '/');

        // Prepare headers
        $headers = [];
        if (isset($opts['headers']) && is_array($opts['headers'])) {
            $headers = $opts['headers'];
            unset($opts['headers']);
        }

        // Set content type for POST requests
        if (strtoupper($method) === 'POST' && !isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if ($isTokenPath) {
            $headers['Authorization'] = 'Bearer ' . $this->client->getApiKey();
        } else {
            // Lazily mint (or refresh a nearly-expired) access token.
            $this->client->ensureAccessToken();
            $headers['Authorization'] = 'Bearer ' . $this->client->getAccessToken();
        }

        try {
            $response = $this->httpClient->request($method, $url, $headers, $params);
        } catch (ApiErrorException $exception) {
            if ($isTokenPath || $exception->getHttpStatus() !== 401) {
                throw $exception;
            }

            // The token was revoked/expired server-side: mint a fresh one and
            // retry exactly once, then let any failure surface.
            $this->client->refreshAccessToken();
            $headers['Authorization'] = 'Bearer ' . $this->client->getAccessToken();

            $response = $this->httpClient->request($method, $url, $headers, $params);
        }

        // Unwrap the {status, message, errors, error_code, data} envelope.
        // array_key_exists: a success with "data": null must yield an empty
        // resource, not a resource built from the envelope itself.
        if (array_key_exists('data', $response)) {
            return is_array($response['data']) ? $response['data'] : [];
        }

        return $response;
    }

    /**
     * Build query string from parameters
     *
     * @param array<string, mixed> $params Parameters
     * @return string Query string
     */
    protected function buildQueryString(array $params): string
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
