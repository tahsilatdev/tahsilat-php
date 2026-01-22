<?php

declare(strict_types=1);

namespace Tahsilat\Service;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Exception\InvalidRequestException;
use Tahsilat\Resource\Refund;
use Tahsilat\Resource\ResolvePreAuth;
use Tahsilat\Resource\TransactionResult;

/**
 * Service for transaction operations
 *
 * @package Tahsilat\Service
 */
class TransactionService extends AbstractService
{
    /**
     * Retrieve a transaction by ID
     *
     * @param int|string $transactionId Transaction ID
     * @param array<string, mixed> $opts Request options
     * @return TransactionResult Transaction result resource
     * @throws AuthenticationException
     * @throws ApiErrorException
     * @throws InvalidRequestException
     */
    public function retrieve($transactionId, array $opts = []): TransactionResult
    {
        if (empty($transactionId)) {
            throw new InvalidRequestException('Transaction ID is required');
        }

        try {
            $response = $this->request('get', '/transaction/' . $transactionId, [], $opts);

            return new TransactionResult($response);
        } catch (ApiErrorException $e) {
            if ($e->getErrorCode() === 2004 || $e->getCode() === 404) {
                throw new InvalidRequestException(
                    "Transaction not found: {$transactionId}",
                    404,
                    $e->getErrorCode()
                );
            }

            throw $e;
        }
    }

    /**
     * Create a new refund
     *
     * @param array<string, mixed> $params Refund parameters
     * @param array<string, mixed> $opts Request options
     * @return Refund Refund resource
     * @throws AuthenticationException
     */
    public function refund(array $params = [], array $opts = []): Refund
    {
        $response = $this->request('post', '/transaction/refund', $params, $opts);

        return new Refund($response);
    }

    /**
     * Resolve a pre-authorization
     *
     * @param array<string, mixed> $params Pre-auth parameters
     * @param array<string, mixed> $opts Request options
     * @return ResolvePreAuth ResolvePreAuth resource
     * @throws AuthenticationException
     */
    public function resolvePreAuth(array $params = [], array $opts = []): ResolvePreAuth
    {
        $response = $this->request('post', '/transaction/resolve-pre-auth', $params, $opts);

        return new ResolvePreAuth($response);
    }
}
