<?php

namespace GingerPayments\Payments\Builders;

use GingerPluginSdk\Collections\Transactions;
use GingerPluginSdk\Entities\Order;
use GingerPluginSdk\Entities\PaymentMethodDetails;
use GingerPluginSdk\Entities\Transaction;
use GingerPluginSdk\Properties\Amount;
use GingerPluginSdk\Properties\Currency;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;
use OxidEsales\EshopCommunity\Core\Registry;

class OrderBuilder
{
    /**
     * @param float $totalAmount
     * @param OxidOrder $order
     * @param string $paymentMethod
     * @param string $returnUrl
     * @param string $webhookUrl
     * @return Order
     */
    public static function buildOrder(float $totalAmount, OxidOrder $order, string $paymentMethod, string $returnUrl, string $webhookUrl): Order
    {
        // Build order entity
        $currency = new Currency(value: $order->getOrderCurrency()->name);
        $amount = new Amount(value: (int)($totalAmount * 100));

        $paymentMethodDetails = null;
        if ($paymentMethod === 'ideal') {
            $paymentMethodDetails = new PaymentMethodDetails();
            $paymentMethodDetails->setPaymentMethodDetailsIdeal('');
        }


        $transaction = new Transactions(new Transaction(paymentMethod: $paymentMethod, paymentMethodDetails: $paymentMethodDetails));
        return new Order(
            currency: $currency,
            amount: $amount,
            transactions: $transaction,
            customer: CustomerBuilder::buildCustomer($order),
            webhook_url: $webhookUrl,
            return_url: $returnUrl,
            id: $order->getId(),
            merchantOrderId: $order->getId(),
            description: "Oxid order " .  " at " . $order->getShopId()
        );
    }
}
