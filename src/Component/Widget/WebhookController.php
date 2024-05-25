<?php

namespace GingerPayments\Payments\Component\Widget;

use Exception;
use GingerPayments\Payments\Helpers\GingerApiHelper;
use GingerPayments\Payments\PSP\PSPConfig;
use GingerPluginSdk\Entities\Order;
use GingerPluginSdk\Exceptions\APIException;
use JsonException as JsonExceptionAlias;
use OxidEsales\Eshop\Core\WidgetControl;
use OxidEsales\EshopCommunity\Core\Registry;
use Symfony\Component\Routing\Annotation\Route;


class WebhookController extends WidgetControl
{
    protected GingerApiHelper $gingerApiHelper;

    public function __construct()
    {
        parent::__construct();
        require_once PSPConfig::AUTOLOAD_FILE;

    }

    public function setClassKey()
    {
    }

    public function setFncName()
    {
    }

    public function setViewParameters()
    {
    }

    /**
     * @return string
     * @throws JsonExceptionAlias
     */
    public function init(): string
    {
        $data = $this->getApiData();
        $orderId = $this->getOrderId();
        return $this->handleWebhook(data: $data, orderId:  $orderId, gingerOrder:  $gingerOrder = $this->handleApiOrder(data: $data));
    }

    /**
     * @return mixed
     * @throws JsonExceptionAlias
     */
    private function getApiData(): mixed
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true, 512, JSON_THROW_ON_ERROR);
    }

    private function getOrderId(): string|null
    {
        return Registry::getRequest()->getRequestParameter('ox_order');
    }

    /**
     * @param $apiStatus
     * @return string
     */
    private function mapStatus($apiStatus): string
    {
        return match ($apiStatus) {
            'completed' => 'PAID',
            'processing' => 'PROCESSING',
            'cancelled' => 'CANCELLED',
            'expired' => 'EXPIRED',
            default => 'NEW',
        };
    }

    /**
     * @param $data
     * @param $orderId
     * @param $gingerOrder
     * @return int
     */
    private function handleWebhook($data, $orderId, $gingerOrder): int
    {
        if (!$orderId) {
            http_response_code(404);
            return print " Order ID is missing";
        }

        if ($data['event'] !== "transaction_status_changed") {
            http_response_code(400);
            return print " Event is not transaction_status_changed";
        }

        $apiOrderStatus = $gingerOrder->getStatus()->get();
        $oxidOrderStatus = $this->mapStatus($apiOrderStatus);
        $order = oxNew(\oxorder::class);

        if ($order->load($orderId)) {
            $order->oxorder__oxtransstatus = new \OxidEsales\Eshop\Core\Field($oxidOrderStatus);
            switch ($oxidOrderStatus) {
                case 'EXPIRED':
                case 'CANCELLED':
                    $order->oxorder__oxstorno = new \OxidEsales\Eshop\Core\Field(1);
                    break;
                case 'PAID':
                    $order->oxorder__oxpaid = new \OxidEsales\Eshop\Core\Field(date('Y-m-d H:i:s'));
                    break;
            }
            $order->save();

            $newStatus = $order->oxorder__oxtransstatus->value;
            http_response_code(200);
            return print " Order status updated successfully to $newStatus";
        }
        http_response_code(404);
        return print " Order not found";
    }


    /**
     * @param $data
     * @return Order
     * @throws Exception
     */
    private function handleApiOrder($data): Order
    {
        $gingerOrderId = $data['order_id'];
        return (new GingerApiHelper())->getOrder($gingerOrderId);
    }
}

