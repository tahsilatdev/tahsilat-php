<?php

namespace Tahsilat\Service;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Exception\InvalidRequestException;
use Tahsilat\Resource\Refund;
use Tahsilat\Resource\ResolvePreAuth;
use Tahsilat\Resource\TransactionResult;

/**
 * Service for customer operations
 *
 * @package Tahsilat\Service
 */
class TransactionService extends AbstractService
{
    /**
     * @param $transactionId
     * @param $opts
     * @return TransactionResult
     * @throws AuthenticationException
     * @throws ApiErrorException|InvalidRequestException
     */
    public function retrieve($transactionId, $opts = [])
    {
        try {
            if (empty($transactionId)) {
                throw new InvalidRequestException('Transaction ID is required');
            }

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
     * @param array<string, mixed> $params parameters
     * @param array<string, mixed> $opts Request options
     * @return Refund Refund resource
     * @throws AuthenticationException
     */
    public function refund($params = [], $opts = [])
    {
        $response = $this->request('post', '/transaction/refund', $params, $opts);

        return new Refund($response);
    }

    /**
     * @param array<string, mixed> $params parameters
     * @param array<string, mixed> $opts Request options
     * @return ResolvePreAuth
     * @throws AuthenticationException
     */
    public function resolvePreAuth($params = [], $opts = [])
    {
        $response = $this->request('post', '/transaction/resolve-pre-auth', $params, $opts);

        return new ResolvePreAuth($response);
    }
}