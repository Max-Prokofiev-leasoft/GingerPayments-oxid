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
        $this->gingerApiHelper = new GingerApiHelper(endpoint: $this->getEndpoint(), apiKey: $this->getApiKey());
    }

    /**
     * @throws APIException
     */
    public function processPayment($totalAmount, $order, $paymentMethod): string
    {
        $returnUrl = $this->getReturnUrl();
        $order = OrderBuilder::buildOrder(
            totalAmount: $totalAmount,
            order: $order,
            paymentMethod: $paymentMethod,
            returnUrl: $returnUrl
        );
        return $this->gingerApiHelper->sendOrder(order: $order)->getPaymentUrl();
    }

    public function getApiKey(): string
    {
        $moduleSettingService = ContainerFacade::get(ModuleSettingServiceInterface::class);
        $apiKey = $moduleSettingService->getString('gingerpayment_apikey', 'gingerpayments')->toString();

        if (!$this->isValidApiKeyFormat($apiKey)) {
            throw new \InvalidArgumentException('Invalid API key format.');
        }
        return $apiKey;
    }

    public function getEndpoint(): string
    {
        return PSPConfig::ENDPOINT;
    }

    private function isValidApiKeyFormat(string $apiKey): bool
    {
        if (!ctype_alnum($apiKey)) {
            return false;
        }

        if (preg_match('/[\'";--]|(\/\*)|(\*\/)|(\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION|JOIN|CREATE|ALTER|TRUNCATE|REPLACE)\b)/i', $apiKey)) {
            return false;
        }

        if (preg_match('/<script|<\/script>|javascript:/i', $apiKey)) {
            return false;
        }

        return true;
    }

    public function getReturnUrl(): string
    {
        $config = \oxregistry::getConfig();
        return $config->getShopUrl() . 'index.php?cl=thankyou';
    }
}
