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
    private object $_oPaymentInfo;

    /**
     * Sets payment parameters.
     *
     * @param object $oUserpayment User payment object
     */
    public function setPaymentParams($oUserpayment)
    {
        // store data
        $this->_oPaymentInfo = &$oUserpayment;
    }

    /**
     * @throws APIException
     */
    public function executePayment($dAmount, &$oOrder)
    {
        $this->_iLastErrorNo = null;
        $this->_sLastError = null;

        if (@$this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value === 'gingerpaymentscreditcard') {

            $payment_redirect = $this->paymentHelper->processPayment(dAmount: $dAmount,oOrder: $oOrder,paymentMethod: 'credit-card');
            header("Location: $payment_redirect");
            exit();

        }

        return false;
    }


}