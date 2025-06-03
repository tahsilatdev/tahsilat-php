<?php

namespace Tahsilat\Exception;

/**
 * Base exception class for Tahsilat SDK
 *
 * @package Tahsilat\Exception
 */
class TahsilatException extends \Exception
{
    /**
     * @var int|null Error code from API
     */
    protected $errorCode;

    /**
     * @var array<string, mixed>|null Response data
     */
    protected $responseData;

    /**
     * Constructor
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param int|null $errorCode API error code
     * @param array<string, mixed>|null $responseData Response data
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct($message = '', $code = 0, $errorCode = null, $responseData = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->responseData = $responseData;
    }

    /**
     * Get API error code
     *
     * @return int|null API error code
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
    public function getResponseData()
    {
        return $this->responseData;
    }
}