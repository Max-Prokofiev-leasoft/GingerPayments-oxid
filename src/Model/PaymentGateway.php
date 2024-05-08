<?php

namespace GingerPayments\Payments\Model;

use Ginger\ApiClient;
use http\Client;
use OxidEsales\EshopCommunity\Application\Model\PaymentGateway as BasePaymentGateway;
use OxidEsales\Eshop\Core\Field;


class PaymentGateway extends BasePaymentGateway
{
    private $o_paymentInfo;
    private $_oOrderInfo;

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

    public function executePayment($dAmount, &$oOrder)
    {
        $this->_iLastErrorNo = null;
        $this->_sLastError = null;

//        if (!$this->isActive()) {
//            return true; // fake yes
//        }
//        die(var_dump($oOrder));

        // proceed with no payment
        // used for other countries
//        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);


        if (@$this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value === 'gingerpaymentscreditcard') {
            $currency[] = $oOrder->getOrderCurrency();

           $order = [
               "currency" => $currency[0]->name,
               "amount" => $dAmount * 10,
               "description" => 'Order with id: '.$oOrder->getId(),
               "return_url" => 'http://localhost/en/Vehicles/',
               "transactions" => [
                   "payment_method" => "credit-card"
               ]
           ];
//            $orderCreated = $api->createOrder($order);

           die(print_r($orderCreated, true));
        }

        return false;
    }


}