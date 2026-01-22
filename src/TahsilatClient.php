<?php

declare(strict_types=1);

namespace Tahsilat;

use Tahsilat\Exception\ApiErrorException;
use Tahsilat\Exception\AuthenticationException;
use Tahsilat\Service\AbstractService;
use Tahsilat\Service\CommissionService;
use Tahsilat\Service\CustomerService;
use Tahsilat\Service\PaymentService;
use Tahsilat\Service\ProductService;
use Tahsilat\Service\TokenService;
use Tahsilat\Service\TransactionService;
use Tahsilat\Service\BinLookupService;

/**
 * Client for interacting with the Tahsilat API
 *
 * @property-read TransactionService $transactions
 * @property-read CustomerService $customers
 * @property-read PaymentService $payments
 * @property-read ProductService $products
 * @property-read TokenService $tokens
 * @property-read CommissionService $commissions
 * @property-read BinLookupService $binLookup
 *
 * @package Tahsilat
 */
class TahsilatClient
{
    /**
     * @var string The API key for authentication
     */
    private string $apiKey;

    /**
     * @var string|null The access token
     */
    private ?string $accessToken = null;

    /**
     * @var array<string, mixed> Client configuration options
     */
    private array $config;

    /**
     * @var array<string, AbstractService> Service instances
     */
    private array $services = [];

    /**
     * @var array<string, string> Service class mapping
     */
    private const SERVICE_MAP = [
        'transactions' => TransactionService::class,
        'customers' => CustomerService::class,
        'payments' => PaymentService::class,
        'products' => ProductService::class,
        'tokens' => TokenService::class,
        'commissions' => CommissionService::class,
        'binLookup' => BinLookupService::class,
    ];

    /**
     * Constructor
     *
     * @param string $apiKey The API key for authentication (sk_test_*, sk_live_*)
     * @param array<string, mixed> $config Configuration options
     * @throws AuthenticationException When API key is invalid
     * @throws ApiErrorException When token fetch fails
     */
    public function __construct(string $apiKey, array $config = [])
    {
        $this->validateApiKey($apiKey);
        $this->apiKey = $apiKey;

        Tahsilat::setApiKey($apiKey);

        $this->config = array_merge($this->getDefaultConfig(), $config);
        $this->applyConfig();

        if (!($config['skip_token_fetch'] ?? false)) {
            $this->fetchAccessToken();
        }
    }

    /**
     * Validate API key format
     *
     * @param string $apiKey The API key to validate
     * @return void
     * @throws AuthenticationException When API key is invalid
     */
    private function validateApiKey(string $apiKey): void
    {
        if (empty($apiKey)) {
            throw new AuthenticationException('API key is required');
        }

        // Sanitize and validate the API key format
        $apiKey = trim($apiKey);
        
        if (!preg_match('/^sk_(live|test)_[a-zA-Z0-9]+$/', $apiKey)) {
            throw new AuthenticationException(
                'Invalid API key format. API key must start with "sk_live_" or "sk_test_". ' .
                'Public keys (pk_*) cannot be used for server-side API calls.'
            );
        }
    }

    /**
     * Get default configuration
     *
     * @return array<string, mixed> Default configuration
     */
    private function getDefaultConfig(): array
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
    private function applyConfig(): void
    {
        if (isset($this->config['api_version'])) {
            Tahsilat::setApiVersion((string) $this->config['api_version']);
        }
        if (isset($this->config['max_retries'])) {
            Tahsilat::setMaxRetries((int) $this->config['max_retries']);
        }
        if (isset($this->config['connect_timeout'])) {
            Tahsilat::setConnectTimeout((int) $this->config['connect_timeout']);
        }
        if (isset($this->config['timeout'])) {
            Tahsilat::setTimeout((int) $this->config['timeout']);
        }
        if (isset($this->config['verify_ssl_certs'])) {
            Tahsilat::setVerifySslCerts((bool) $this->config['verify_ssl_certs']);
        }
        if (isset($this->config['ca_bundle_path'])) {
            Tahsilat::setCaBundlePath($this->config['ca_bundle_path']);
        }
    }

    /**
     * Fetch access token using the API key
     *
     * @return void
     * @throws ApiErrorException When token fetch fails
     * @throws AuthenticationException When authentication fails
     */
    private function fetchAccessToken(): void
    {
        $tokenService = new TokenService($this);
        $token = $tokenService->getToken();

        if ($token !== null && isset($token->access_token) && !empty($token->access_token)) {
            $this->accessToken = (string) $token->access_token;
            Tahsilat::setAccessToken($this->accessToken);
        } else {
            throw new ApiErrorException('Failed to fetch access token', 0, null, null);
        }
    }

    /**
     * Magic getter for services
     *
     * @param string $name Service name
     * @return AbstractService
     * @throws \InvalidArgumentException When service doesn't exist
     */
    public function __get(string $name): AbstractService
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        if (isset(self::SERVICE_MAP[$name])) {
            $serviceClass = self::SERVICE_MAP[$name];
            $this->services[$name] = new $serviceClass($this);
            return $this->services[$name];
        }

        throw new \InvalidArgumentException("Property {$name} does not exist");
    }

    /**
     * Get the API key
     *
     * @return string The API key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Set the API key
     *
     * @param string $apiKey The API key
     * @return void
     * @throws ApiErrorException When token fetch fails
     * @throws AuthenticationException When authentication fails
     */
    public function setApiKey(string $apiKey): void
    {
        $this->validateApiKey($apiKey);
        $this->apiKey = $apiKey;
        Tahsilat::setApiKey($apiKey);

        // Clear cached services as they may use the old token
        $this->services = [];
        
        $this->fetchAccessToken();
    }

    /**
     * Get the access token
     *
     * @return string|null The access token
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Set the access token
     *
     * @param string $accessToken The access token
     * @return void
     */
    public function setAccessToken(string $accessToken): void
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
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Set configuration value
     *
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     * @return void
     */
    public function setConfig(string $key, $value): void
    {
        $this->config[$key] = $value;
        $this->applyConfig();
    }
}
