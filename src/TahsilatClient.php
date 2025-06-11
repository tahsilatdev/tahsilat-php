<?php

namespace Tahsilat;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Service\CommissionService;
use Tahsilat\Service\CustomerService;
use Tahsilat\Service\PaymentService;
use Tahsilat\Service\ProductService;
use Tahsilat\Service\TokenService;
use Tahsilat\Service\TransactionService;

/**
 * Client for interacting with the Tahsilat API
 *
 * @property TransactionService $transactions
 * @property CustomerService $customers
 * @property PaymentService $payments
 * @property ProductService $products
 * @property TokenService $tokens
 * @property CommissionService $commissions
 *
 * @package Tahsilat
 */
class TahsilatClient
{
    /**
     * @var string The API key for authentication
     */
    private $apiKey;

    /**
     * @var string|null The access token
     */
    private $accessToken;

    /**
     * @var array<string, mixed> Client configuration options
     */
    private $config;

    /**
     * @var array<string, Service\AbstractService> Service instances
     */
    private $services = [];

    /**
     * Constructor
     *
     * @param string $apiKey The API key for authentication (sk_test_*, pk_test_*, sk_live_*, pk_live_*)
     * @param array<string, mixed> $config Configuration options
     */
    public function __construct($apiKey, $config = [])
    {
        $this->apiKey = $apiKey;

        // Set global API key
        Tahsilat::setApiKey($apiKey);

        // Apply configuration
        $this->config = array_merge($this->getDefaultConfig(), $config);
        $this->applyConfig();

        // Auto-fetch token on initialization
        if (!isset($config['skip_token_fetch']) || !$config['skip_token_fetch']) {
            $this->fetchAccessToken();
        }
    }

    /**
     * Get default configuration
     *
     * @return array<string, mixed> Default configuration
     */
    private function getDefaultConfig()
    {
        return [
            'api_version' => 'v1',
            'max_retries' => 3,
            'connect_timeout' => 30,
            'timeout' => 80,
            'verify_ssl_certs' => true,
            'ca_bundle_path' => null,
        ];
    }

    /**
     * Apply configuration to Tahsilat static class
     *
     * @return void
     */
    private function applyConfig()
    {
        if (isset($this->config['api_version'])) {
            Tahsilat::setApiVersion($this->config['api_version']);
        }
        if (isset($this->config['max_retries'])) {
            Tahsilat::setMaxRetries($this->config['max_retries']);
        }
        if (isset($this->config['connect_timeout'])) {
            Tahsilat::setConnectTimeout($this->config['connect_timeout']);
        }
        if (isset($this->config['timeout'])) {
            Tahsilat::setTimeout($this->config['timeout']);
        }
        if (isset($this->config['verify_ssl_certs'])) {
            Tahsilat::setVerifySslCerts($this->config['verify_ssl_certs']);
        }
        if (isset($this->config['ca_bundle_path'])) {
            Tahsilat::setCaBundlePath($this->config['ca_bundle_path']);
        }
    }

    /**
     * Fetch access token using the API key
     *
     * @return void
     * @throws ApiErrorException|Exception\AuthenticationException When token fetch fails
     */
    private function fetchAccessToken()
    {
        $tokenService = new TokenService($this);
        $token = $tokenService->getToken();

        if ($token && isset($token->access_token)) {
            $this->accessToken = $token->access_token;
            Tahsilat::setAccessToken($this->accessToken);
        } else {
            throw new ApiErrorException('Failed to fetch access token', 0, null, null);
        }
    }

    /**
     * Magic getter for services
     *
     * @param string $name Service name
     * @return Service\AbstractService
     * @throws \InvalidArgumentException When service doesn't exist
     */
    public function __get($name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        $serviceClass = $this->getServiceClass($name);
        if ($serviceClass !== null) {
            $this->services[$name] = new $serviceClass($this);
            return $this->services[$name];
        }

        throw new \InvalidArgumentException("Property {$name} does not exist");
    }

    /**
     * Get service class name from property name
     *
     * @param string $name Property name
     * @return string|null Service class name
     */
    private function getServiceClass($name)
    {
        $services = [
            'transactions' => 'Tahsilat\\Service\\TransactionService',
            'customers' => 'Tahsilat\\Service\\CustomerService',
            'payments' => 'Tahsilat\\Service\\PaymentService',
            'products' => 'Tahsilat\\Service\\ProductService',
            'tokens' => 'Tahsilat\\Service\\TokenService',
        ];

        return isset($services[$name]) ? $services[$name] : null;
    }

    /**
     * Get the API key
     *
     * @return string The API key
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set the API key
     *
     * @param string $apiKey The API key
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        Tahsilat::setApiKey($apiKey);

        // Refresh token with new API key
        $this->fetchAccessToken();
    }

    /**
     * Get the access token
     *
     * @return string|null The access token
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the access token
     *
     * @param string $accessToken The access token
     * @return void
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        Tahsilat::setAccessToken($accessToken);
    }

    /**
     * Get configuration value
     *
     * @param string $key Configuration key
     * @param mixed $default Default value
     * @return mixed Configuration value
     */
    public function getConfig($key, $default = null)
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    /**
     * Set configuration value
     *
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     * @return void
     */
    public function setConfig($key, $value)
    {
        $this->config[$key] = $value;
        $this->applyConfig();
    }
}