<?php

namespace GingerPayments\Payments\Controller;

use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Exception\ArticleInputException;
use OxidEsales\Eshop\Core\Exception\NoArticleException;
use OxidEsales\Eshop\Core\Exception\OutOfStockException;
use OxidEsales\Eshop\Core\Registry;

class ModuleOrderController extends OrderController
{
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
     * Executes the order process and handles API redirect logic if the payment method matches.
     *
     * @return string|null
     * - Returns the next step if successful, 'user' if no user, or null if an error occurs.
     */
    public function execute(): ?string
    {
        $session = Registry::getSession();
        if (!$session->checkSessionChallenge()) {
            return null;
        }

        if (!$this->validateTermsAndConditions()) {
            $this->_blConfirmAGBError = 1;
            return null;
        }

        $user = $this->getUser();
        if (!$user) {
            return 'user';
        }

        // get basket contents
        $basket = $session->getBasket();
        if ($basket->getProductsCount()) {
            try {
                $order = oxNew(Order::class);
                $iSuccess = $order->finalizeOrder($basket, $user);
                $user->onOrderExecute($basket, $iSuccess);

                if ($iSuccess === Order::ORDER_STATE_OK && $this->isGingerPaymentMethod($order->oxorder__oxpaymenttype->value)) {
                    $apiUrl = $session->getVariable('payment_url');
                    Registry::getUtils()->redirect($apiUrl, true, 302);
                }
                Registry::getLogger()->error('Not Redirected');
                return $this->getNextStep($iSuccess);
            } catch (OutOfStockException $oEx) {
                $oEx->setDestination('basket');
                Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'basket');
            } catch (NoArticleException $oEx) {
                Registry::getUtilsView()->addErrorToDisplay($oEx);
            } catch (ArticleInputException $oEx) {
                Registry::getUtilsView()->addErrorToDisplay($oEx);
            }
        }
        return null;
    }

    /**
     * Checks if the given payment method is a custom API payment method.
     *
     * @param string $paymentId
     * Selected payment method ID from the OXID
     * @return bool
     * - Returns true if the payment method is a custom API payment method, otherwise false.
     */
    private function isGingerPaymentMethod(string $paymentId): bool
    {
        $paymentMethods = [
            'gingerpaymentsideal',
            'gingerpaymentscreditcard'
        ];

        return in_array($paymentId, $paymentMethods, true);
    }
}
