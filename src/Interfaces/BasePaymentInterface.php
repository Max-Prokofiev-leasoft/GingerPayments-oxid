<?php

namespace GingerPayments\Payments\Interfaces;

use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;

interface BasePaymentInterface
{
    /**
     * @param float $amount
     * @param OxidOrder $order
     * @return string
     */
    public function handlePayment(float $amount, OxidOrder $order): string;

}