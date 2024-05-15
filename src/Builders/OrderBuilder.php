<?php

namespace GingerPayments\Payments\Builders;

use GingerPluginSdk\Collections\Transactions;
use GingerPluginSdk\Entities\Order;
use GingerPluginSdk\Entities\Transaction;
use GingerPluginSdk\Properties\Amount;
use GingerPluginSdk\Properties\Currency;

class OrderBuilder
{
    public static function buildOrder($totalAmount, $order, $paymentMethod): Order
    {
        // Build order entity
        $currency = new Currency(value: $order->getOrderCurrency()->name);
        $amount = new Amount(value: (int)($totalAmount * 100));

        $transaction = new Transactions(new Transaction($paymentMethod));

        return new Order(
            currency: $currency,
            amount: $amount,
            transactions: $transaction,
            customer: CustomerBuilder::buildCustomer($order),
            id: $order->getId()
        );
    }
}
