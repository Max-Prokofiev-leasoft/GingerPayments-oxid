<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Tests\Integration\Service;

use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use GingerPayments\Payments\Model\GreetingTracker;
use GingerPayments\Payments\Service\Repository;
use GingerPayments\Payments\Service\Tracker as TrackerService;
use GingerPayments\Payments\Tests\Integration\IntegrationTestCase;

final class TrackerTest extends IntegrationTestCase
{
    public const TEST_TRACKER_ID = '_testoxid';

    public const TEST_USER_ID = '_testuser';

    public const TEST_GREETING = 'Hi there';

    public function testUpdateTrackerNoGreetingChange(): void
    {
        $repo = $this->getRepositoryMock(self::TEST_GREETING);

        $repo->expects($this->never())
            ->method('getTrackerByUserId');

        /** @var TrackerService $tracker */
        $tracker = new TrackerService($repo);

        $tracker->updateTracker($this->getUserModel());
    }

    public function testUpdateTrackerGreetingChange(): void
    {
        $repo = $this->getRepositoryMock(self::TEST_GREETING . ' with a change');

        $repo->expects($this->once())
            ->method('getTrackerByUserId')
            ->willReturn($this->getGreetingTrackerMock());

        /** @var TrackerService $tracker */
        $tracker = new TrackerService($repo);

        $tracker->updateTracker($this->getUserModel());
    }

    /**
     * NOTE: this user model is NOT saved to database
     */
    private function getUserModel(): EshopModelUser
    {
        $user = oxNew(EshopModelUser::class);
        $user->assign(
            [
                'oxid'         => self::TEST_USER_ID,
                'oemtgreeting' => self::TEST_GREETING,
            ]
        );

        return $user;
    }

    private function getGreetingTrackerMock(): GreetingTracker
    {
        $tracker = $this->getMockBuilder(GreetingTracker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tracker->expects($this->once())
            ->method('countUp');

        return $tracker;
    }

    private function getRepositoryMock(string $result): Repository
    {
        $repo = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->once())
            ->method('getSavedUserGreeting')
            ->willReturn($result);

        return $repo;
    }
}
