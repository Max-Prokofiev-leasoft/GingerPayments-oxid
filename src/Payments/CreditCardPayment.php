<?php

namespace GingerPayments\Payments\Payments;

use GingerPayments\Payments\Helpers\PaymentHelper;
use GingerPayments\Payments\Interfaces\BasePaymentInterface;
use GingerPluginSdk\Exceptions\APIException;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;

class CreditCardPayment implements BasePaymentInterface
{
    private PaymentHelper $paymentHelper;

    public function __construct()
    {
        $this->paymentHelper = new PaymentHelper();
    }

    /**
     * @param float $amount
     * @param OxidOrder $order
     * @return string
     * @throws APIException
     */
    public function handlePayment(float $amount, OxidOrder $order): string
    {
        return $this->paymentHelper->processPayment(
            totalAmount: $amount,
            order: $order,
            paymentMethod: "credit-card",
        );
    }
}