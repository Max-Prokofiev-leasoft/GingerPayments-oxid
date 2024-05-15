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

    public function __construct($endpoint = PSPConfig::ENDPOINT ,$apiKey = PSPConfig::API_KEY)
    {
        $clientOptions = new ClientOptions(endpoint: $endpoint, useBundle: true, apiKey: $apiKey);
        $this->client = new Client(options: $clientOptions);
    }

    /**
     * @throws APIException
     */
    public function sendOrder(Order $order): Order
    {
        // Send order to Ginger Payments API
        return $this->client->sendOrder($order);
    }
}
