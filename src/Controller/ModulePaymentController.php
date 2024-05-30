<?php

namespace GingerPayments\Payments\Controller;

use GingerPayments\Payments\Helpers\GingerApiHelper;
use GingerPayments\Payments\PSP\PSPConfig;
use GingerPluginSdk\Properties\Currency;
use OxidEsales\Eshop\Application\Controller\PaymentController;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\DeliverySetList;

class ModulePaymentController extends PaymentController
{
    private GingerApiHelper $gingerApiHelper;

    /**
     * Constructor to initialize GingerApiHelper.
     */
    public function __construct()
    {
        parent::__construct();
        require_once PSPConfig::AUTOLOAD_FILE;
        $this->gingerApiHelper = new GingerApiHelper();
    }

    /**
     * Initializes the controller.
     * Calls the parent init method.
     *
     * @return void
     */
    public function init(): void
    {
        parent::init();
    }

    /**
     * Maps OXID payment ID to Ginger Plugin payment method name.
     *
     * @param string $paymentId
     * Payment ID from OXID
     * @return string
     * - Valid payment name if it's a Payment Method from Ginger Plugin
     */
    private function mapPaymentMethod($paymentId): string
    {
        return match ($paymentId) {
            'gingerpaymentscreditcard' => 'credit-card',
            'gingerpaymentsideal' => 'ideal',
            default => $paymentId,
        };
    }

    /**
     * Retrieves and returns the list of available payment methods.
     *
     * This method checks if the payment list is already set. If not, it attempts to retrieve the active shipping set from the request parameters or session.
     * Then, it gets the current basket and the delivery set data including all available sets, the active shipping set, and the payment list.
     * The shipping method for the basket is set, and each payment method is checked for availability using the specified currency.
     * Finally, it calculates the payment expenses for preview and sets the payment list.
     *
     * @return array|null
     * - Returns the list of available payment methods or null if none are available.
     */
    public function getPaymentList(): mixed
    {
        if ($this->_oPaymentList === null) {
            $this->_oPaymentList = false;

            $sActShipSet = Registry::getRequest()->getRequestEscapedParameter('sShipSet');
            if (!$sActShipSet) {
                $sActShipSet = Registry::getSession()->getVariable('sShipSet');
            }

            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $oBasket = $session->getBasket();

            list($aAllSets, $sActShipSet, $aPaymentList) =
                Registry::get(DeliverySetList::class)->getDeliverySetData($sActShipSet, $this->getUser(), $oBasket);

            $oBasket->setShipping($sActShipSet);

            $shopCurrency = Registry::getConfig()->getActShopCurrencyObject()->name;
            $currency = new Currency($shopCurrency);

            foreach ($aPaymentList as $paymentId => $payment) {
                $mappedPaymentMethod = $this->mapPaymentMethod($paymentId);
                if (($mappedPaymentMethod !== $paymentId) && !$this->gingerApiHelper->client->checkAvailabilityForPaymentMethodUsingCurrency($mappedPaymentMethod, $currency)) {
                    unset($aPaymentList[$paymentId]);
                }
            }

            // calculating payment expences for preview for each payment
            $this->setValues($aPaymentList, $oBasket);
            $this->_oPaymentList = $aPaymentList;
            $this->_aAllSets = $aAllSets;
        }

        return $this->_oPaymentList;
    }

}
