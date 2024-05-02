<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Model;

use GingerPayments\Payments\Service\BasketItemLogger;
use GingerPayments\Payments\Traits\ServiceContainer;

/**
 * @see \OxidEsales\Eshop\Application\Model\Basket
 */
class Basket extends Basket_parent
{
    use ServiceContainer;

    /**
     * Method overrides eShop method and adds logging functionality.
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addToBasket(
        $productID,
        $amount,
        $sel = null,
        $persParam = null,
        $shouldOverride = false,
        $isBundle = false,
        $oldBasketItemId = null
    ) {
        $basketItemLogger = $this->getServiceFromContainer(BasketItemLogger::class);
        $basketItemLogger->log($productID);

        return parent::addToBasket($productID, $amount, $sel, $persParam, $shouldOverride, $isBundle, $oldBasketItemId);
    }
}
