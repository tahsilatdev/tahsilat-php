<?php

namespace Tahsilat\Service;

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
     */
    public function getToken($opts = [])
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
     */
    public function create($opts = [])
    {
        return $this->getToken($opts);
    }
}