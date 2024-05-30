<?php

namespace GingerPayments\Payments\Helpers;

use GingerPayments\Payments\Builders\OrderBuilder;
use GingerPayments\Payments\PSP\PSPConfig;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;
use GingerPluginSdk\Exceptions\APIException;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;

/**
 * Class PaymentHelper
 * Provides helper functions for processing payments through the Ginger Payments API.
 */
class PaymentHelper
{
    protected GingerApiHelper $gingerApiHelper;

    /**
     * Constructor to initialize GingerApiHelper.
     */
    public function __construct()
    {
        $this->gingerApiHelper = new GingerApiHelper();
    }

    /**
     * Processes the payment for a given order and return URL on API.
     *
     * @param float $totalAmount
     * Total amount from the OXID order
     * @param OxidOrder $order
     * OXID order
     * @param string $paymentMethod
     * Payment method name
     * @return string
     * - URL to process payment
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

    /**
     * Retrieves the return URL for the SDK Order.
     *
     * @return string
     * - URL to thank you page
     */
    private function getReturnUrl(): string
    {
        $shopUrl = $this->getShopUrl();
        $sessionId = Registry::getSession()->getId();
        return $shopUrl . 'index.php?cl=thankyou&sid=' . $sessionId;
    }

    /**
     * Retrieves the shop URL.
     *
     * @return string
     * - Shop URL
     */
    private function getShopUrl(): string
    {
        return Registry::getConfig()->getShopUrl();
    }

    /**
     * Retrieves the webhook URL for SDK Order.
     *
     * @param string $orderId
     * OXID Order ID
     * @return string
     * - Webhook URL
     */
    private function getWebhookUrl($orderId): string
    {
        $shopUrl = "https://a6a1-193-109-145-122.ngrok-free.app" . "/";
        return $shopUrl . "widget.php/?cl=webhook&ox_order=" . $orderId;
    }
}