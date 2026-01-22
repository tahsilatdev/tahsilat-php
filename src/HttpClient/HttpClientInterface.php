<?php

declare(strict_types=1);

namespace Tahsilat\HttpClient;

/**
 * Interface for HTTP client implementations
 *
 * @package Tahsilat\HttpClient
 */
interface HttpClientInterface
{
    /**
     * Send an HTTP request
     *
     * @param string $method HTTP method
     * @param string $url Request URL
     * @param array<string, string> $headers Request headers
     * @param array<string, mixed> $params Request parameters
     * @return array<string, mixed> Response data
     */
    public function request(string $method, string $url, array $headers = [], array $params = []): array;
}
