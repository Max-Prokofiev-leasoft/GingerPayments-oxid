<?php

namespace GingerPayments\Payments\Controller;

use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Registry;

class ModuleOrderController extends OrderController
{
    public function init()
    {
        parent::init();
    }
    /**
     * Executes parent::execute(), adds API redirect logic if payment method matches.
     *
     * @return string|null
     */
    public function execute()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        if (!$session->checkSessionChallenge()) {
            return;
        }

        if (!$this->validateTermsAndConditions()) {
            $this->_blConfirmAGBError = 1;

            return;
        }

        // additional check if we really really have a user now
        $oUser = $this->getUser();
        if (!$oUser) {
            return 'user';
        }

        // get basket contents
        $oBasket = $session->getBasket();
        if ($oBasket->getProductsCount()) {
            try {
                $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);

                //finalizing ordering process (validating, storing order into DB, executing payment, setting status ...)
                $iSuccess = $oOrder->finalizeOrder($oBasket, $oUser);

                // performing special actions after user finishes order (assignment to special user groups)
                $oUser->onOrderExecute($oBasket, $iSuccess);

                if ($iSuccess === Order::ORDER_STATE_OK && $this->isGingerPaymentMethod($oOrder->oxorder__oxpaymenttype->value)) {
                    $apiUrl = $session->getVariable('payment_url');
                    Registry::getUtils()->redirect($apiUrl, true, 302);
                    exit; // Stop further code execution
                }
                // proceeding to next view
                return $this->getNextStep($iSuccess);
            } catch (\OxidEsales\Eshop\Core\Exception\OutOfStockException $oEx) {
                $oEx->setDestination('basket');
                Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'basket');
            } catch (\OxidEsales\Eshop\Core\Exception\NoArticleException $oEx) {
                Registry::getUtilsView()->addErrorToDisplay($oEx);
            } catch (\OxidEsales\Eshop\Core\Exception\ArticleInputException $oEx) {
                Registry::getUtilsView()->addErrorToDisplay($oEx);
            }
        }
    }

    /**
     * Check if the payment method is a custom API payment method
     *
     * @param string $paymentType
     * @return bool
     */
    private function isGingerPaymentMethod(string $paymentType): bool
    {
        $paymentMethods = [
            'gingerpaymentsideal',
            'gingerpaymentscreditcard'
        ];

        return in_array($paymentType, $paymentMethods, true);
    }
}
