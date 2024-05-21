<?php

namespace GingerPayments\Payments\Helpers;

use GingerPayments\Payments\Builders\OrderBuilder;
use GingerPayments\Payments\PSP\PSPConfig;
use GingerPluginSdk\Exceptions\APIException;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;

class PaymentHelper
{

    protected GingerApiHelper $gingerApiHelper;

    /**
     * @throws APIException
     */
    public function __construct()
    {
        $this->gingerApiHelper = new GingerApiHelper(endpoint: $this->getEndpoint(),apiKey:  $this->getApiKey());
    }

    /**
     * @throws APIException
     */
    public function processPayment($totalAmount, $order,$paymentMethod): string
    {
        $order = OrderBuilder::buildOrder(totalAmount: $totalAmount,order: $order,paymentMethod: $paymentMethod) ;
        return $this->gingerApiHelper->sendOrder($order)->getPaymentUrl();
    }
    public function getApiKey(): string
    {
        $moduleSettingService = ContainerFacade::get(ModuleSettingServiceInterface::class);
        return $moduleSettingService->getString('gingerpayment_apikey', 'gingerpayments')->toString();
    }
    public function getEndpoint(): string
    {
        return PSPConfig::ENDPOINT;
    }
}
