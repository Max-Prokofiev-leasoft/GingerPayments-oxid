<?php

namespace GingerPayments\Payments\Model;


use GingerPayments\Payments\Helpers\PaymentHelper;
use GingerPluginSdk\Exceptions\APIException;


class PaymentGateway
{
    public function __construct()
    {
        require_once PaymentHelper::AUTOLOAD_FILE;
        $this->paymentHelper = new PaymentHelper();
    }

    private PaymentHelper $paymentHelper;
    private object $paymentInfo;

    /**
     * Sets payment parameters.
     *
     * @param object $userPayment User payment object
     */
    public function setPaymentParams($userPayment)
    {
        // store data
        $this->paymentInfo = &$userPayment;
    }

    /**
     * @throws APIException
     */
    public function executePayment($amount, &$order)
    {
        $paymentMethods = [
            'gingerpaymentsideal' => 'ideal',
            'gingerpaymentscreditcard' => 'credit-card'
        ];

        $paymentId = @$this->paymentInfo->oxuserpayments__oxpaymentsid->value;

        if (isset($paymentMethods[$paymentId])) {
            $paymentMethod = $paymentMethods[$paymentId];
            $payment_redirect = $this->paymentHelper->processPayment(
                totalAmount: $amount,
                order: $order,
                paymentMethod: $paymentMethod
            );
            header("Location: $payment_redirect");
            exit();
        }
        return false;
    }


}