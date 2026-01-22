<?php

declare(strict_types=1);

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\HttpClient\CurlClient;
use Tahsilat\HttpClient\HttpClientInterface;
use Tahsilat\Tahsilat;
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
     */
    public function __construct(TahsilatClient $client)
    {
        $this->client = $client;
        $this->httpClient = new CurlClient();
    }

    /**
     * Make an API request
     *
     * @param string $method HTTP method
     * @param string $path API path
     * @param array<string, mixed> $params Request parameters
     * @param array<string, mixed> $opts Request options
     * @return array<string, mixed> Response data
     * @throws AuthenticationException When API key is missing
     */
    protected function request(string $method, string $path, array $params = [], array $opts = []): array
    {
        // Check API key
        if (Tahsilat::getApiKey() === null) {
            throw new AuthenticationException(
                'API key is required. Set it using Tahsilat::setApiKey() or instantiate TahsilatClient with an API key.'
            );
        }

        // Build URL
        $url = Tahsilat::getApiBase() . ltrim($path, '/');

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

        // Make request
        $response = $this->httpClient->request($method, $url, $headers, $params);

        // Handle successful response
        if (isset($response['data'])) {
            return $response['data'];
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
