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

class GingerApiHelper
{
    public Client $client;

    /**
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
     * @param Order $order
     * @return Order
     * @throws APIException
     */
    public function sendOrder(Order $order): Order
    {
        try {
            // Send order to Ginger Payments API
            return $this->client->sendOrder($order);
        } catch (APIException $e) {
            throw new APIException("Error sending order: " . $e->getMessage());
        }
    }

    /**
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
     * @param $orderId
     * @return Order
     * @throws \Exception
     */
    public function getOrder($orderId): Order
    {
        return $this->client->getOrder($orderId);

    }

    public function getEndpoint(): string
    {
        return PSPConfig::ENDPOINT;
    }

    public function getApiKey(): string
    {
        $moduleSettingService = ContainerFacade::get(ModuleSettingServiceInterface::class);
        $apiKey = $moduleSettingService->getString('gingerpayments_apikey', 'gingerpayments')->toString();

        if (!$this->isValidApiKeyFormat($apiKey)) {
            throw new \InvalidArgumentException('Invalid API key format.');
        }
        return $apiKey;
    }
}
