<?php

namespace GingerPayments\Payments\Helpers;

use GingerPayments\Payments\Builders\OrderBuilder;
use GingerPluginSdk\Exceptions\APIException;

class PaymentHelper
{
    public const AUTOLOAD_FILE = __DIR__ . '/../../vendor/autoload.php';
    protected GingerApiHelper $gingerApiHelper;

    public function __construct()
    {

        $this->gingerApiHelper = new GingerApiHelper();
    }

    /**
     * @throws APIException
     */
    public function processPayment($totalAmount, $order,$paymentMethod): string
    {
        $order = OrderBuilder::buildOrder(totalAmount: $totalAmount,order: $order,paymentMethod: $paymentMethod) ;
        return $this->gingerApiHelper->sendOrder($order)->getPaymentUrl();
    }
}
