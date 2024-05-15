<?php

namespace GingerPayments\Payments\Helpers;

use GingerPayments\Payments\Builders\OrderBuilder;
use GingerPluginSdk\Exceptions\APIException;

class PaymentHelper
{
    protected GingerApiHelper $gingerApiHelper;

    public function __construct()
    {

        $this->gingerApiHelper = new GingerApiHelper();
    }

    /**
     * @throws APIException
     */
    public function processPayment($dAmount, $oOrder,$paymentMethod): string
    {
        $order = OrderBuilder::buildOrder(totalAmount: $dAmount,order: $oOrder,paymentMethod: $paymentMethod) ;
        return $this->gingerApiHelper->sendOrder($order)->getPaymentUrl();
    }
}
