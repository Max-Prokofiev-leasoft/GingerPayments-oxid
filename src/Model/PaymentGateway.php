<?php

namespace GingerPayments\Payments\Model;

use OxidEsales\EshopCommunity\Application\Model\PaymentGateway as BasePaymentGateway;
use OxidEsales\Eshop\Core\Field;


class PaymentGateway extends  BasePaymentGateway
{
    private $o_paymentInfo;

    /**
     * Sets payment parameters.
     *
     * @param object $oUserpayment User payment object
     */
    public function setPaymentParams($oUserpayment)
    {
        // store data
        $this->_oPaymentInfo = & $oUserpayment;
    }
    public function executePayment($dAmount, &$oOrder)
    {
        $this->_iLastErrorNo = null;
        $this->_sLastError = null;

//        if (!$this->isActive()) {
//            return true; // fake yes
//        }

        // proceed with no payment
        // used for other countries
        if (@$this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value === 'gingerpaymentscreditcard') {
            die('success');
        }

        return false;
    }


}