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

    public function __construct()
    {
        parent::__construct();
        require_once PSPConfig::AUTOLOAD_FILE;
        $this->gingerApiHelper = new GingerApiHelper();
    }

    public function init()
    {
        parent::init();
    }

    private function mapPaymentMethod($paymentId): string
    {
        return match ($paymentId) {
            'gingerpaymentscreditcard' => 'credit-card',
            'gingerpaymentsideal' => 'ideal',
            default => $paymentId,
        };
    }

    public function getPaymentList()
    {
        if ($this->_oPaymentList === null) {
            $this->_oPaymentList = false;

            $sActShipSet = Registry::getRequest()->getRequestEscapedParameter('sShipSet');
            if (!$sActShipSet) {
                $sActShipSet = Registry::getSession()->getVariable('sShipSet');
            }

            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $oBasket = $session->getBasket();

            // load sets, active set, and active set payment list
            list($aAllSets, $sActShipSet, $aPaymentList) =
                Registry::get(DeliverySetList::class)->getDeliverySetData($sActShipSet, $this->getUser(), $oBasket);

            $oBasket->setShipping($sActShipSet);

            // перевірка валюти для кожного методу оплати
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
