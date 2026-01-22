<?php

declare(strict_types=1);

namespace Tahsilat\Service;

use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\Token;

/**
 * Service for token operations
 *
 * @package Tahsilat\Service
 */
class TokenService extends AbstractService
{
    /**
     * Get a new access token
     *
     * @param array<string, mixed> $opts Request options
     * @return Token Token resource
     * @throws AuthenticationException
     */
    public function getToken(array $opts = []): Token
    {
        // For token endpoint, we need to use different headers
        if (!isset($opts['headers'])) {
            $opts['headers'] = [];
        }
        $opts['headers']['Content-Type'] = 'application/json';

        $response = $this->request('post', '/token/get-token', [], $opts);

        return new Token($response);
    }

    /**
     * Create a new token (alias for getToken)
     *
     * @param array<string, mixed> $opts Request options
     * @return Token Token resource
     * @throws AuthenticationException
     */
    public function create(array $opts = []): Token
    {
        return $this->getToken($opts);
    }
}
