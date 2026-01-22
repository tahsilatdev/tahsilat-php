<?php

/**
 * Tahsilat PHP SDK
 *
 * @package Tahsilat
 * @version 2.0.0
 * @requires PHP >= 7.4.0
 */

// PHP version check - minimum 7.4.0 required
if (PHP_VERSION_ID < 70400) {
    trigger_error(
        sprintf(
            'Tahsilat PHP SDK requires PHP version 7.4.0 or higher. Current version: %s. ' .
            'PHP versions below 7.4 are no longer supported.',
            PHP_VERSION
        ),
        E_USER_ERROR
    );

    exit(1);
}

// Required extensions check
$requiredExtensions = ['curl', 'json', 'mbstring', 'openssl'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    trigger_error(
        sprintf(
            'Tahsilat PHP SDK requires the following PHP extensions: %s',
            implode(', ', $missingExtensions)
        ),
        E_USER_ERROR
    );

    exit(1);
}

// Base classes
require_once __DIR__ . '/src/Tahsilat.php';
require_once __DIR__ . '/src/TahsilatClient.php';

// HTTP Client
require_once __DIR__ . '/src/HttpClient/HttpClientInterface.php';
require_once __DIR__ . '/src/HttpClient/CurlClient.php';

// Exceptions
require_once __DIR__ . '/src/Exception/TahsilatException.php';
require_once __DIR__ . '/src/Exception/ApiErrorException.php';
require_once __DIR__ . '/src/Exception/AuthenticationException.php';
require_once __DIR__ . '/src/Exception/InvalidRequestException.php';
require_once __DIR__ . '/src/Exception/NetworkException.php';
require_once __DIR__ . '/src/Exception/SignatureVerificationException.php';

// Resources
require_once __DIR__ . '/src/Resource/ApiResource.php';
require_once __DIR__ . '/src/Resource/Token.php';
require_once __DIR__ . '/src/Resource/Payment.php';
require_once __DIR__ . '/src/Resource/Customer.php';
require_once __DIR__ . '/src/Resource/Product.php';
require_once __DIR__ . '/src/Resource/Refund.php';
require_once __DIR__ . '/src/Resource/WebhookEvent.php';
require_once __DIR__ . '/src/Resource/Commission.php';
require_once __DIR__ . '/src/Resource/BinLookup.php';
require_once __DIR__ . '/src/Resource/TransactionResult.php';
require_once __DIR__ . '/src/Resource/ResolvePreAuth.php';

// Services
require_once __DIR__ . '/src/Service/AbstractService.php';
require_once __DIR__ . '/src/Service/TokenService.php';
require_once __DIR__ . '/src/Service/PaymentService.php';
require_once __DIR__ . '/src/Service/CustomerService.php';
require_once __DIR__ . '/src/Service/ProductService.php';
require_once __DIR__ . '/src/Service/TransactionService.php';
require_once __DIR__ . '/src/Service/CommissionService.php';
require_once __DIR__ . '/src/Service/BinLookupService.php';

// Utilities
require_once __DIR__ . '/src/Util/RequestOptions.php';
require_once __DIR__ . '/src/Util/Webhook.php';
require_once __DIR__ . '/src/Util/StatusConstants.php';