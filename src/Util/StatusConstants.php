<?php

namespace Tahsilat\Util;

/**
 * Tahsilat SDK Constants
 *
 * @package Tahsilat\Util
 */
class StatusConstants
{
    /**
     * Payment status constants
     */
    const PAYMENT_STATUS_SUCCESS = 1;
    const PAYMENT_STATUS_FAILED = 2;
    const PAYMENT_STATUS_INCOMPLETE = 3;

    /**
     * Transaction status constants
     */
    const TRANSACTION_STATUS_PENDING = 1;
    const TRANSACTION_STATUS_COMPLETED = 2;
    const TRANSACTION_STATUS_PRE_AUTH = 3;
    const TRANSACTION_STATUS_CANCELLED = 4;
    const TRANSACTION_STATUS_REFUNDED = 5;
    const TRANSACTION_STATUS_PARTIAL_REFUND = 6;
    const TRANSACTION_STATUS_DISPUTE = 7;
    const TRANSACTION_STATUS_FRAUD = 8;
    const TRANSACTION_STATUS_TIMEOUT = 9;

    /**
     * Payment method constants
     */
    const PAYMENT_METHOD_3D = 1;
    const PAYMENT_METHOD_2D = 2;
}