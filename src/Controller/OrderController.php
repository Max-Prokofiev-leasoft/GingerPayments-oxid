<?php

namespace GingerPayments\Payments\Controller;

use GingerPluginSdk\Exceptions\APIException;
use OxidEsales\Eshop\Core\Registry;
use GingerPayments\Payments\Model\PaymentGateway;

class OrderController extends \OxidEsales\Eshop\Application\Controller\OrderController
{
    public function setClassKey($classKey)
    {
        parent::setClassKey($classKey);
    }

    public function execute()
    {
        $session = Registry::getSession();
        if (!$session->checkSessionChallenge()) {
            return;
        }

        if (!$this->validateTermsAndConditions()) {
            $this->_blConfirmAGBError = 1;
            return;
        }

        $oUser = $this->getUser();
        if (!$oUser) {
            return 'user';
        }

        // get basket contents
        $oBasket = $session->getBasket();
        if ($oBasket->getProductsCount()) {
            try {
                $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);

                // finalizing ordering process (validating, storing order into DB, executing payment, setting status ...)
                $iSuccess = $oOrder->finalizeOrder($oBasket, $oUser);

                // performing special actions after user finishes order (assignment to special user groups)
                $oUser->onOrderExecute($oBasket, $iSuccess);

                // proceeding to next view
                return $this->getNextStep($iSuccess);
            } catch (\OxidEsales\Eshop\Core\Exception\OutOfStockException $oEx) {
                $oEx->setDestination('basket');
                Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'basket');
            } catch (\OxidEsales\Eshop\Core\Exception\NoArticleException $oEx) {
                Registry::getUtilsView()->addErrorToDisplay($oEx);
            } catch (\OxidEsales\Eshop\Core\Exception\ArticleInputException $oEx) {
                Registry::getUtilsView()->addErrorToDisplay($oEx);
            } catch (APIException $e) {
            }
        }
    }

    /**
     * Returns next order step. If ordering was sucessfull - returns string "thankyou" (possible
     * additional parameters), otherwise - returns string "payment" with additional
     * error parameters.
     *
     * @param integer $iSuccess status code
     *
     * @return  string  $sNextStep  partial parameter url for next step
     */
    protected function getNextStep($iSuccess): string
    {
        $sNextStep = 'thankyou';

        //little trick with switch for multiple cases
        switch (true) {
            case ($iSuccess === \OxidEsales\Eshop\Application\Model\Order::ORDER_STATE_MAILINGERROR):
                $sNextStep = 'thankyou?mailerror=1';
                break;
            case ($iSuccess === \OxidEsales\Eshop\Application\Model\Order::ORDER_STATE_INVALIDDELADDRESSCHANGED):
                $sNextStep = 'order?iAddressError=1';
                break;
            case ($iSuccess === \OxidEsales\Eshop\Application\Model\Order::ORDER_STATE_BELOWMINPRICE):
                $sNextStep = 'order';
                break;
            case ($iSuccess === \OxidEsales\Eshop\Application\Model\Order::ORDER_STATE_VOUCHERERROR):
                $sNextStep = 'basket';
                break;
            case ($iSuccess === \OxidEsales\Eshop\Application\Model\Order::ORDER_STATE_PAYMENTERROR):
                // no authentication, kick back to payment methods
                Registry::getSession()->setVariable('payerror', 2);
                $sNextStep = 'payment?payerror=2';
                break;
            case ($iSuccess === \OxidEsales\Eshop\Application\Model\Order::ORDER_STATE_ORDEREXISTS):
                break; // reload blocker activ
            case (is_numeric($iSuccess) && $iSuccess > 3):
                Registry::getSession()->setVariable('payerror', $iSuccess);
                $sNextStep = 'payment?payerror=' . $iSuccess;
                break;
            case (!is_numeric($iSuccess) && $iSuccess):
                //instead of error code getting error text and setting payerror to -1
                Registry::getSession()->setVariable('payerror', -1);
                $iSuccess = urlencode($iSuccess);
                $sNextStep = 'payment?payerror=-1&payerrortext=' . $iSuccess;
                break;
            default:
                break;
        }

        return $sNextStep;
    }
}

