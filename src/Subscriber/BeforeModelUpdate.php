<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

#AfterModelUpdateEvent

declare(strict_types=1);

namespace GingerPayments\Payments\Subscriber;

use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeModelUpdateEvent;
use GingerPayments\Payments\Service\Tracker;
use GingerPayments\Payments\Traits\ServiceContainer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @extendable-class
 */
class BeforeModelUpdate implements EventSubscriberInterface
{
    use ServiceContainer;

    public function handle(BeforeModelUpdateEvent $event): BeforeModelUpdateEvent
    {
        $payload = $event->getModel();

        if (is_a($payload, EshopModelUser::class)) {
            $this->getServiceFromContainer(Tracker::class)
                ->updateTracker($payload);
        }

        return $event;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeModelUpdateEvent::class => 'handle',
        ];
    }
}
