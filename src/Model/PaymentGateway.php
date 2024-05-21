<?php

namespace GingerPayments\Payments\Model;

use GingerPayments\Payments\Helpers\PaymentHelper;
use GingerPayments\Payments\PSP\PSPConfig;
use GingerPluginSdk\Exceptions\APIException;
use OxidEsales\EshopCommunity\Application\Model\Order;

class PaymentGateway
{
    public function __construct()
    {
        require_once PSPConfig::AUTOLOAD_FILE;
        $this->paymentHelper = new PaymentHelper();
    }

    private PaymentHelper $paymentHelper;
    private object $paymentInfo;

    /**
     * Sets payment parameters.
     *
     * @param object $userPayment User payment object
     */
    public function setPaymentParams(object $userPayment): void
    {
        // store data
        $this->paymentInfo = &$userPayment;
    }

    /**
     * Handles payment execution based on the selected payment method.
     *
     * @param float $amount Payment amount
     * @param Order $order Order object
     * @param string $paymentMethod Selected payment method
     * @throws APIException
     */
    private function handlePayment(float $amount, Order $order, string $paymentMethod): void
    {
        $payment_redirect = $this->paymentHelper->processPayment(
            totalAmount: $amount,
            order: $order,
            paymentMethod: $paymentMethod
        );

        $utils = \oxregistry::getUtils();
        $utils->redirect($payment_redirect);
    }

    /**
     * Executes payment based on the selected payment method.
     *
     * @param float $amount Payment amount
     * @param Order $order Order object
     * @return bool True on successful execution, false otherwise
     * @throws APIException
     */
    public function executePayment(float $amount, Order $order): bool
    {
        $o = oxNew(Order::class);

        $paymentMethods = [
            'gingerpaymentsideal' => 'ideal',
            'gingerpaymentscreditcard' => 'credit-card'
        ];

        $paymentId = @$this->paymentInfo->oxuserpayments__oxpaymentsid->value;

        if (isset($paymentMethods[$paymentId])) {
            $paymentMethod = $paymentMethods[$paymentId];
            $this->handlePayment($amount, $order, $paymentMethod);
            return true;
        }
        return false;
    }
}
