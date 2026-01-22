<?php

declare(strict_types=1);

namespace Tahsilat\Exception;

/**
 * Exception for API errors
 *
 * @package Tahsilat\Exception
 */
class ApiErrorException extends TahsilatException
{
    /**
     * Get validation errors if any
     *
     * @return array<string, mixed>|null Validation errors
     */
    public function getValidationErrors(): ?array
    {
        if ($this->responseData !== null && isset($this->responseData['errors']) && !empty($this->responseData['errors'])) {
            return $this->responseData['errors'];
        }
        return null;
    }

    /**
     * Check if this is a validation error
     *
     * @return bool Whether this is a validation error
     */
    public function isValidationError(): bool
    {
        return $this->errorCode === 901 || $this->getValidationErrors() !== null;
    }
}
