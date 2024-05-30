<?php

namespace GingerPayments\Payments\Builders;

use GingerPluginSdk\Collections\OrderLines;
use GingerPluginSdk\Entities\Line;
use GingerPluginSdk\Properties\Amount;
use GingerPluginSdk\Properties\Currency;
use GingerPluginSdk\Properties\VatPercentage;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;

class OrderLinesBuilder
{
    /**
     * Builds OrderLines object from the given OXID order.
     *
     * @param OxidOrder $order
     * OXID Order
     * @return OrderLines
     * - SDK OrderLines object
     */
    public static function buildOrderLines(OxidOrder $order): OrderLines
    {
        $orderArticles = $order->getOrderArticles();
        $lines = [];

        foreach ($orderArticles as $orderArticle) {
            $article = $orderArticle->getArticle();

            $discountRateValue = null;
            if (isset($orderArticle->oxorderarticles__oxdiscount) && $orderArticle->oxorderarticles__oxdiscount->value !== null) {
                $discountRateValue = (int)($orderArticle->oxorderarticles__oxdiscount->value * 100);
            }

            $line = new Line(
                type: 'physical',
                merchantOrderLineId: $orderArticle->getId(),
                name: $article->oxarticles__oxtitle->value,
                quantity: (int)$orderArticle->oxorderarticles__oxamount->value,
                amount: new Amount((int)($orderArticle->oxorderarticles__oxbrutprice->value * 100)),
                vatPercentage: new VatPercentage((int)($orderArticle->oxorderarticles__oxvat->value * 100)),
                currency: new Currency($order->getOrderCurrency()->name),
                discountRate: $discountRateValue,
                url: $article->getLink()
            );

            $lines[] = $line;
        }

        if ($order->oxorder__oxdelcost->value > 0) {
            $lines[] = self::getShippingOrderLine($order);
        }

        return new OrderLines(...$lines);
    }

    /**
     * Creates a shipping order line.
     *
     * @param OxidOrder $order
     * OXID Order
     * @return Line
     * - SDK Line object for shipping
     */
    protected static function getShippingOrderLine(OxidOrder $order): Line
    {
        $shippingAmount = (float)(
        $order->oxorder__oxdelcost->value
        );

        return new Line(
            type: 'shipping_fee',
            merchantOrderLineId: 'Shipping',
            name: self::getShippingName($order),
            quantity: 1,
            amount: new Amount((int)($shippingAmount * 100)),
            vatPercentage: new VatPercentage((int)(0)),
            currency: new Currency($order->getOrderCurrency()->name)
        );
    }

    /**
     * Retrieves the shipping name from the order.
     *
     * @param OxidOrder $order
     * OXID Order
     * @return string
     * - Shipping name
     */
    protected static function getShippingName(OxidOrder $order): string
    {
        return preg_replace("/[^A-Za-z0-9 ]/", "", $order->oxorder__oxdeltype->value);
    }
}
