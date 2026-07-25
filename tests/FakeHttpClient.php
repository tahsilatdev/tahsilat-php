<?php

declare(strict_types=1);

namespace Tahsilat\Tests;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\HttpClient\HttpClientInterface;

/**
 * Scripted HTTP client test double: queues one handler per expected request
 * and records every call (url + headers) for assertions.
 */
class FakeHttpClient implements HttpClientInterface
{
    /** @var array<int, callable|array<string, mixed>> Queued responses */
    private array $queue = [];

    /** @var array<int, array{method: string, url: string, headers: array<string, string>, params: array<string, mixed>}> */
    public array $calls = [];

    /**
     * @param callable|array<string, mixed> $response Array response or callable(url, headers): array
     * @return void
     */
    public function queue($response): void
    {
        $this->queue[] = $response;
    }

    /**
     * Queues an ApiErrorException carrying the given HTTP status.
     *
     * @param int $httpStatus
     * @return void
     */
    public function queueHttpError(int $httpStatus, string $message = 'error'): void
    {
        $this->queue[] = static function () use ($httpStatus, $message): array {
            $exception = new ApiErrorException($message, $httpStatus);
            $exception->setHttpStatus($httpStatus);

            throw $exception;
        };
    }

    /**
     * @param string $method
     * @param string $url
     * @param array<string, string> $headers
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function request(string $method, string $url, array $headers = [], array $params = []): array
    {
        $this->calls[] = ['method' => $method, 'url' => $url, 'headers' => $headers, 'params' => $params];

        if ($this->queue === []) {
            throw new \RuntimeException('FakeHttpClient: no queued response for ' . $url);
        }

        $next = array_shift($this->queue);

        if (is_callable($next)) {
            return $next($url, $headers);
        }

        return $next;
    }
}
