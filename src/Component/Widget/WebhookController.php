<?php

namespace GingerPayments\Payments\Component\Widget;

use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Core\Registry;
use Symfony\Component\Routing\Annotation\Route;


class WebhookController extends WidgetControl
{
    public function setClassKey(){
    }
    public function setFncName()
    {
    }
    public function setViewParameters(){}

    /**
     * @return string
     * @throws \JsonException
     */
    public function init(): string
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $session = Registry::getSession();
        $orderId = Registry::getRequest()->getRequestParameter('ox_order');
        $transactionStatus = $data['transaction_status'];

        if (!$orderId) {
            header("HTTP/1.1 400 Bad Request");
            return "Order ID is missing";
        }

        $order = oxNew(\oxorder::class);
        if ($order->load($orderId)) {
            $order->oxorder__oxtransstatus = new \OxidEsales\Eshop\Core\Field($transactionStatus);
            $order->save();
            header("HTTP/1.1 200 OK");
            return "Order updated successfully";
        }

        header("HTTP/1.1 404 Not Found");
        return "Order not found";
    }
}
