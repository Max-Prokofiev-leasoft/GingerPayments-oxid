<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Service;

use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use GingerPayments\Payments\Model\User as ModelUser;
use GingerPayments\Payments\Service\Repository as RepositoryService;

/**
 * @extendable-class
 */
class Tracker
{
    /** @var RepositoryService */
    private $repository;

    public function __construct(RepositoryService $repository)
    {
        $this->repository = $repository;
    }

    public function updateTracker(EshopModelUser $user): void
    {
        $savedGreeting = $this->repository->getSavedUserGreeting($user->getId());

        /** @var ModelUser $user */
        if ($savedGreeting !== $user->getPersonalGreeting()) {
            $tracker = $this->repository->getTrackerByUserId($user->getId());
            $tracker->countUp();
        }
    }
}
