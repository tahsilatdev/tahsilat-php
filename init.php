<?php

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

/**
 * Tahsilat PHP SDK
 *
 * @package Tahsilat
 */

// PHP version check
if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    throw new Exception('Tahsilat PHP SDK requires PHP version 5.6 or higher.');
}

// Required extensions check
$requiredExtensions = ['curl', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        throw new Exception("Tahsilat PHP SDK requires the {$ext} extension.");
    }
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
