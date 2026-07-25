<?php

declare(strict_types=1);

namespace Tahsilat\Exception;

use Exception;
use Throwable;

/**
 * Base exception class for Tahsilat SDK
 *
 * @package Tahsilat\Exception
 */
class TahsilatException extends Exception
{
    /**
     * @var int|string|null Error code from API
     */
    protected $errorCode;

    /**
     * @var array<string, mixed>|null Response data
     */
    protected ?array $responseData;

    /**
     * @var int|null HTTP status code of the failed response
     */
    protected ?int $httpStatus = null;

    /**
     * Constructor
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param int|string|null $errorCode API error code
     * @param array<string, mixed>|null $responseData Response data
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        $errorCode = null,
        ?array $responseData = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->responseData = $responseData;
    }

    /**
     * Get API error code
     *
     * @return int|string|null API error code
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get response data
     *
     * @return array<string, mixed>|null Response data
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    /**
     * Set the HTTP status code of the failed response
     *
     * @param int $httpStatus HTTP status code
     * @return void
     */
    public function setHttpStatus(int $httpStatus): void
    {
        $this->httpStatus = $httpStatus;
    }

    /**
     * Get the HTTP status code of the failed response
     *
     * @return int|null HTTP status code
     */
    public function getHttpStatus(): ?int
    {
        return $this->httpStatus;
    }
}
