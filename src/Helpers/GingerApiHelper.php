<?php

namespace GingerPayments\Payments\Helpers;

use GingerPayments\Payments\PSP\PSPConfig;
use GingerPluginSdk\Client;
use GingerPluginSdk\Properties\ClientOptions;
use GingerPluginSdk\Entities\Order;
use GingerPluginSdk\Exceptions\APIException;
use GingerPluginSdk\Properties\Currency;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;

/**
 * Class GingerApiHelper
 * Provides helper functions for interacting with the Ginger Payments API.
 */
class GingerApiHelper
{
    public Client $client;

    /**
     * Initializes the Ginger API client with the endpoint and API key.
     *
     * @throws APIException
     */
    public function __construct()
    {
        try {
            $clientOptions = new ClientOptions(endpoint: $this->getEndpoint(), useBundle: true, apiKey: $this->getApiKey());
            $this->client = new Client(options: $clientOptions);
        } catch (\Exception $e) {
            throw new APIException("Failed to initialize Ginger API client: " . $e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * Sends an order to the Ginger Payments API.
     *
     * @param Order $order
     * SDK Order
     * @return Order
     * @throws APIException
     */
    public function sendOrder(Order $order): Order
    {
        try {
            return $this->client->sendOrder($order);
        } catch (APIException $e) {
            throw new APIException("Error sending order: " . $e->getMessage());
        }
    }

    /**
     * Validates the format of the API key to ensure it is safe and correct.
     *
     * @param string $apiKey
     * @return bool
     */
    private function isValidApiKeyFormat(string $apiKey): bool
    {
        // Ensure API key is alphanumeric and doesn't contain SQL or JavaScript injection patterns
        return ctype_alnum($apiKey) &&
            !preg_match('/[\'";\-\-]|(\/\*)|(\*\/)|(\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION|JOIN|CREATE|ALTER|TRUNCATE|REPLACE)\b)/i', $apiKey) &&
            !preg_match('/<script|<\/script>|javascript:/i', $apiKey);
    }

    /**
     * Retrieves an order from the Ginger API by order ID.
     *
     * @param string $orderId
     * SDK Order ID
     * @return Order
     * - SDK Order
     * @throws \Exception
     */
    public function getOrder(string $orderId): Order
    {
        return $this->client->getOrder($orderId);

    }

    /**
     * Retrieves the endpoint URL for the Ginger Payments API.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return PSPConfig::ENDPOINT;
    }

    /**
     * Retrieves and validates the API key from the module settings.
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getApiKey(): string
    {
        $moduleSettingService = ContainerFacade::get(ModuleSettingServiceInterface::class);
        $apiKey = $moduleSettingService->getString('gingerpayments_apikey', 'gingerpayments')->toString();

        if (!$this->isValidApiKeyFormat(apiKey: $apiKey)) {
            throw new \InvalidArgumentException('Invalid API key format.');
        }
        return $apiKey;
    }
}
