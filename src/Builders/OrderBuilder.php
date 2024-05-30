<?php

namespace GingerPayments\Payments\Builders;

use GingerPluginSdk\Collections\OrderLines;
use GingerPluginSdk\Collections\Transactions;
use GingerPluginSdk\Entities\Order;
use GingerPluginSdk\Entities\PaymentMethodDetails;
use GingerPluginSdk\Entities\Transaction;
use GingerPluginSdk\Properties\Amount;
use GingerPluginSdk\Properties\Currency;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;

class OrderBuilder
{
    /**
     * Builds an SDK Order object from the given OXID order data.
     *
     * @param float $totalAmount
     * OXID Order total amount
     * @param OxidOrder $order
     * OXID Order
     * @param string $paymentMethod
     * Name of the payment method
     * @param string $returnUrl
     * Return url for SDK order
     * @param string $webhookUrl
     * Webhook url for SDK order
     * @return Order
     * - SDK order object
     */
    public static function buildOrder(float $totalAmount, OxidOrder $order, string $paymentMethod, string $returnUrl, string $webhookUrl): Order
    {
        $paymentMethodDetails = self::buildPaymentMethodDetails($paymentMethod);

        return new Order(
            currency: self::buildCurrency(order: $order),
            amount: self::buildAmount(totalAmount: $totalAmount),
            transactions: self::buildTransactions(paymentMethod: $paymentMethod, paymentMethodDetails: $paymentMethodDetails),
            customer: CustomerBuilder::buildCustomer(order: $order),
            orderLines: OrderLinesBuilder::buildOrderLines($order),
            webhook_url: $webhookUrl,
            return_url: $returnUrl,
            id: $order->getId(),
            merchantOrderId: $order->getId(),
            description: self::buildDescription(order: $order),
        );
    }

    /**
     * Builds a Currency object from the given OXID order.
     *
     * @param OxidOrder $order
     * OXID Order
     * @return Currency
     * - SDK Currency object
     */
    private static function buildCurrency(OxidOrder $order): Currency
    {
        return new Currency(value: $order->getOrderCurrency()->name);
    }

    /**
     * Builds an Amount object from the given total amount.
     *
     * @param float $totalAmount
     * Total amount
     * @return Amount
     * - SDK Amount object
     */
    private static function buildAmount(float $totalAmount): Amount
    {
        return new Amount(value: (int)($totalAmount * 100));
    }

    /**
     * Builds PaymentMethodDetails object if needed.
     *
     * @param string $paymentMethod
     * Payment method name
     * @return PaymentMethodDetails|null
     * - SDK PaymentMethodDetails object or null
     */
    private static function buildPaymentMethodDetails(string $paymentMethod): ?PaymentMethodDetails
    {
        if ($paymentMethod === 'ideal') {
            $paymentMethodDetails = new PaymentMethodDetails();
            $paymentMethodDetails->setPaymentMethodDetailsIdeal('');
            return $paymentMethodDetails;
        }
        return null;
    }

    /**
     * Builds Transactions object from the given payment method and details.
     *
     * @param string $paymentMethod
     * Payment method name
     * @param PaymentMethodDetails|null $paymentMethodDetails
     * Payment method details
     * @return Transactions
     * - SDK Transactions object
     */
    private static function buildTransactions(string $paymentMethod, ?PaymentMethodDetails $paymentMethodDetails): Transactions
    {
        return new Transactions(new Transaction(paymentMethod: $paymentMethod, paymentMethodDetails: $paymentMethodDetails));
    }

    /**
     * Builds description for the order.
     *
     * @param OxidOrder $order
     * OXID Order
     * @return string
     * - Description string
     */
    private static function buildDescription(OxidOrder $order): string
    {
        return "Oxid order " . $order->getId() . " at shop " . $order->getShopId();
    }

}
