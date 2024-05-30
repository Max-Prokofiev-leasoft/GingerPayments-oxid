<?php

namespace GingerPayments\Payments\Builders;

use GingerPluginSdk\Collections\OrderLines;
use GingerPluginSdk\Entities\Line;
use GingerPluginSdk\Properties\Amount;
use GingerPluginSdk\Properties\Currency;
use GingerPluginSdk\Properties\VatPercentage;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;
use OxidEsales\EshopCommunity\Application\Model\Article;

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
            /** @var Article $article */
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
        return new OrderLines(...$lines);
    }


}
