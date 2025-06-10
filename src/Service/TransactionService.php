<?php

namespace Tahsilat\Service;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Resource\Refund;
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
     */
    public function retrieve($transactionId, $opts = [])
    {
        try {
            $response = $this->request('get', '/transaction/' . $transactionId, [], $opts);
            return new TransactionResult($response);
        } catch (ApiErrorException $e) {
            if ($e->getCode() === 404) {
                return null;
            }

            throw $e;
        }
    }

    /**
     * Create a new refund
     *
     * @param array<string, mixed> $params Customer parameters
     * @param array<string, mixed> $opts Request options
     * @return Refund Refund resource
     * @throws AuthenticationException
     */
    public function refund($params = [], $opts = [])
    {
        $response = $this->request('post', '/transaction/refund', $params, $opts);

        return new Refund($response);
    }
}