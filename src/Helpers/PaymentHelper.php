<?php

namespace GingerPayments\Payments\Helpers;

use GingerPayments\Payments\Builders\OrderBuilder;
use GingerPayments\Payments\PSP\PSPConfig;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;
use GingerPluginSdk\Exceptions\APIException;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;

class PaymentHelper
{
    protected GingerApiHelper $gingerApiHelper;

    public function __construct()
    {
        $this->gingerApiHelper = new GingerApiHelper();
    }

    /**
     * @param float $totalAmount
     * @param OxidOrder $order
     * @param string $paymentMethod
     * @return string
     * @throws APIException
     */
    public function processPayment(float $totalAmount, OxidOrder $order, string $paymentMethod): string
    {
        $returnUrl = $this->getReturnUrl();
        $webhookUrl = $this->getWebhookUrl($order->getId());
        $orderSdk = OrderBuilder::buildOrder(
            totalAmount: $totalAmount,
            order: $order,
            paymentMethod: $paymentMethod,
            returnUrl: $returnUrl,
            webhookUrl: $webhookUrl

        );
        return $this->gingerApiHelper->sendOrder(order: $orderSdk)->getPaymentUrl();
    }


    private function getReturnUrl(): string
    {
        $shopUrl = $this->getShopUrl();
        $sessionId = Registry::getSession()->getId();
        return $shopUrl . 'index.php?cl=thankyou&sid=' . $sessionId;
    }

    private function getShopUrl(): string
    {
        return Registry::getConfig()->getShopUrl();
    }

    /**
     * @return string
     */
    private function getWebhookUrl($orderId): string
    {
        $shopUrl = "https://9027-193-109-145-122.ngrok-free.app/";
        return $shopUrl . "widget.php/?cl=webhook&ox_order=" . $orderId;
    }
}
//class Ginger_Webhook
//{
//    public function __construct()
//    {
//        $input = json_decode(file_get_contents("php://input"), true);
//        if (!in_array($input['event'], array("status_changed"))) {
//            die("Only work to do if the status changed");
//        }
//
//        $gingerOrderID = $input['order_id'];
//
//        $gingerOrder = $this->ginger_handle_get_order($gingerOrderID);
//        $orderID = $gingerOrder->getMerchantOrderId()->get();
//
//        // Проверка на существование orderID
//        if (!$orderID) {
//            die("Order ID is missing.");
//        }
//
//        $this->update_order_status($orderID, $gingerOrder->getStatus()->get());
//    }
//
//    public function ginger_handle_get_order($gingerOrderID)
//    {
//
//        $gingerClient = Ginger_ClientBuilder::gingerBuildClient();
//
//        try {
//            if ($gingerClient) return $gingerClient->getOrder($gingerOrderID);
//        } catch (Exception $exception) {
//            $exceptionMessage = $exception->getMessage();
//        }
//        $errorMessage = $exceptionMessage ?? "COULD NOT GET ORDER";
//        die($errorMessage);
//    }
//
//    public function update_order_status($orderID, $newStatus)
//    {
//        // Преобразование статуса Ginger в статус Gambio
//        $gambioStatus = $this->map_status($newStatus);
//
//        try {
//            // Обновление статуса заказа в базе данных Gambio
//            $data = array(
//                'orders_status' => $gambioStatus,
//                'last_modified' => 'now()'
//            );
//            xtc_db_perform(TABLE_ORDERS, $data, 'update', 'orders_id = ' . (int)$orderID);
//
//            // Добавление записи в таблицу orders_status_history
//            $data_history = array(
//                'orders_id' => (int)$orderID,
//                'orders_status_id' => $gambioStatus,
//                'date_added' => 'now()',
//                'customer_notified' => '1',
//                'comments' => 'Status updated to ' . $newStatus . ' via Ginger webhook'
//            );
//            xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $data_history);
//
//            echo "Статус ордера успешно обновлен.";
//        } catch (Exception $e) {
//            echo "Произошла ошибка при обновлении статуса ордера: " . $e->getMessage();
//        }
//    }
//
//    private function map_status($gingerStatus)
//    {
//        // Преобразование статуса Ginger в статус Gambio
//        return match ($gingerStatus) {
//            'processing' => 2,
//            'completed' => 3,
//            'cancelled', 'error' => 99,
//            default => 1,
//        };
//    }
//}
//
//new Ginger_Webhook();