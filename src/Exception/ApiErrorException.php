<?php

namespace Tahsilat\Exception;

/**
 * Exception for API errors
 *
 * @package Tahsilat\Exception
 */
class ApiErrorException extends TahsilatException
{
    /**
     * @var array<string, mixed>|null Validation errors
     */
    protected $validationErrors;

    /**
     * Get validation errors if any
     *
     * @return array<string, mixed>|null Validation errors
     */
    public function getValidationErrors()
    {
        if ($this->responseData && isset($this->responseData['errors']) && !empty($this->responseData['errors'])) {
            return $this->responseData['errors'];
        }
        return null;
    }

    /**
     * Check if this is a validation error
     *
     * @return bool Whether this is a validation error
     */
    public function isValidationError()
    {
        return $this->errorCode === 901 || $this->getValidationErrors() !== null;
    }
}