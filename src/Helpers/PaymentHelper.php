<?php

namespace GingerPayments\Payments\Helpers;

use GingerPayments\Payments\Builders\OrderBuilder;
use GingerPayments\Payments\PSP\PSPConfig;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;
use GingerPluginSdk\Exceptions\APIException;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;

class PaymentHelper
{
    protected GingerApiHelper $gingerApiHelper;

    public function __construct()
    {
        $this->gingerApiHelper = new GingerApiHelper();
    }

    /**
     * @param float $totalAmount
     * @param OxidOrder $order
     * @param string $paymentMethod
     * @return string
     * @throws APIException
     */
    public function processPayment(float $totalAmount, OxidOrder $order, string $paymentMethod): string
    {
        $returnUrl = $this->getReturnUrl();
        $webhookUrl = $this->getWebhookUrl($order->getId());
        $orderSdk = OrderBuilder::buildOrder(
            totalAmount: $totalAmount,
            order: $order,
            paymentMethod: $paymentMethod,
            returnUrl: $returnUrl,
            webhookUrl: $webhookUrl

        );
        return $this->gingerApiHelper->sendOrder(order: $orderSdk)->getPaymentUrl();
    }


    private function getReturnUrl(): string
    {
        $shopUrl = $this->getShopUrl();
        $sessionId = Registry::getSession()->getId();
        return $shopUrl . 'index.php?cl=thankyou&sid=' . $sessionId;
    }

    private function getShopUrl(): string
    {
        return Registry::getConfig()->getShopUrl();
    }

    /**
     * @param $orderId
     * @return string
     */
    private function getWebhookUrl($orderId): string
    {
        $shopUrl = "https://e326-193-109-145-122.ngrok-free.app/";
        return $shopUrl . "widget.php/?cl=webhook&ox_order=" . $orderId;
    }
}