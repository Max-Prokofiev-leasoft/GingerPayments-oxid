<?php

namespace GingerPayments\Payments\Model;


use GingerPayments\Payments\Helpers\PaymentHelper;
use GingerPluginSdk\Exceptions\APIException;


class PaymentGateway
{
    public function __construct(){
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
        if (@$this->paymentInfo->oxuserpayments__oxpaymentsid->value === 'gingerpaymentscreditcard') {

            $payment_redirect = $this->paymentHelper->processPayment(totalAmount: $amount,order: $order,paymentMethod: 'credit-card');
            header("Location: $payment_redirect");
            exit();

        }
        return false;
    }


}