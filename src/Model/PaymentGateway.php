<?php

namespace GingerPayments\Payments\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use GingerPayments\Payments\Helpers\PaymentHelper;
use GingerPluginSdk\Exceptions\APIException;


class PaymentGateway
{
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
        $this->paymentHelper = new PaymentHelper();
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