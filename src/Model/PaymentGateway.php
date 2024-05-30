<?php

namespace GingerPayments\Payments\Model;

use GingerPayments\Payments\Helpers\PaymentHelper;
use GingerPayments\Payments\Payments\CreditCardPayment;
use GingerPayments\Payments\Payments\IdealPayment;
use GingerPayments\Payments\PSP\PSPConfig;
use GingerPluginSdk\Exceptions\APIException;
use OxidEsales\EshopCommunity\Application\Model\Order as OxidOrder;
use OxidEsales\EshopCommunity\Core\Registry;

class PaymentGateway
{

    private array $paymentMethods;

    public function __construct()
    {
        require_once PSPConfig::AUTOLOAD_FILE;
        $this->paymentMethods = [
            'gingerpaymentsideal' => new IdealPayment(),
            'gingerpaymentscreditcard' => new CreditCardPayment()
        ];
    }

    private object $paymentInfo;

    /**
     * Sets payment parameters.
     *
     * @param object $userPayment User payment object
     */
    public function setPaymentParams(object $userPayment): void
    {
        // store data
        $this->paymentInfo = &$userPayment;
    }

    /**
     * Executes payment based on the selected payment method.
     *
     * @param float $amount Payment amount
     * @param OxidOrder $order Order object
     * @return bool True on successful execution, false otherwise
     * @throws APIException
     */
    public function executePayment(float $amount, OxidOrder $order): bool
    {

        $paymentId = @$this->paymentInfo->oxuserpayments__oxpaymentsid->value;

        if (isset($this->paymentMethods[$paymentId])) {
            $paymentMethod = $this->paymentMethods[$paymentId];
            $paymentUrl = $paymentMethod->handlePayment(amount:$amount,order: $order);
            Registry::getSession()->setVariable('payment_url', $paymentUrl);
        }
        return true;
    }
}
