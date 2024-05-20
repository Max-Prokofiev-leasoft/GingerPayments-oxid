<?php

namespace GingerPayments\Payments\Helpers;

use GingerPayments\Payments\PSP\PSPConfig;
use GingerPluginSdk\Client;
use GingerPluginSdk\Properties\ClientOptions;
use GingerPluginSdk\Entities\Order;
use GingerPluginSdk\Exceptions\APIException;

class GingerApiHelper
{
    protected Client $client;

    /**
     * @throws APIException
     */
    public function __construct($endpoint = PSPConfig::ENDPOINT , $apiKey = PSPConfig::API_KEY)
    {
        try { $clientOptions = new ClientOptions(endpoint: $endpoint, useBundle: true, apiKey: $apiKey);
            $this->client = new Client(options: $clientOptions);
        }catch (\Exception $e) {
            throw new APIException("Failed to initialize Ginger API client: " . $e->getMessage(), $e->getCode(), $e);

        }

    }

    /**
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
}
