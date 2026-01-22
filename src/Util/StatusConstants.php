<?php

declare(strict_types=1);

namespace Tahsilat\Util;

/**
 * Tahsilat SDK Constants
 *
 * @package Tahsilat\Util
 */
final class StatusConstants
{
    /**
     * Payment status constants
     */
    public const PAYMENT_STATUS_SUCCESS = 1;
    public const PAYMENT_STATUS_FAILED = 2;
    public const PAYMENT_STATUS_INCOMPLETE = 3;

    /**
     * Transaction status constants
     */
    public const TRANSACTION_STATUS_PENDING = 1;
    public const TRANSACTION_STATUS_COMPLETED = 2;
    public const TRANSACTION_STATUS_PRE_AUTHORIZED = 3;
    public const TRANSACTION_STATUS_CANCELLED = 4;
    public const TRANSACTION_STATUS_REFUNDED = 5;
    public const TRANSACTION_STATUS_PARTIAL_REFUNDED = 6;
    public const TRANSACTION_STATUS_CHARGEBACK = 7;
    public const TRANSACTION_STATUS_PARTIAL_CHARGEBACK = 8;
    public const TRANSACTION_STATUS_FRAUD = 9;
    public const TRANSACTION_STATUS_TIMEOUT = 10;

    /**
     * Payment method constants
     */
    public const PAYMENT_METHOD_3D = 1;
    public const PAYMENT_METHOD_2D = 2;

    /**
     * Prevent instantiation
     */
    private function __construct()
    {
    }
}