<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Tests\Integration\Model;

use GingerPayments\Payments\Model\GreetingTracker;
use GingerPayments\Payments\Tests\Integration\IntegrationTestCase;

final class GreetingTrackerTest extends IntegrationTestCase
{
    public const TEST_ID = '_testoxid';

    public function testGetCount(): void
    {
        $this->prepareTestData(22);
        $tracker = oxNew(GreetingTracker::class);
        $tracker->load(self::TEST_ID);

        $this->assertSame(22, $tracker->getCount());
    }

    public function testCountUp(): void
    {
        $this->prepareTestData(10);
        $tracker = oxNew(GreetingTracker::class);
        $tracker->load(self::TEST_ID);

        $tracker->countUp();
        $tracker->countUp();
        $tracker->countUp();

        $this->assertSame(13, $tracker->getCount());
    }

    private function prepareTestData(int $count = 0): string
    {
        $tracker = oxNew(GreetingTracker::class);
        $tracker->assign(
            [
                'oxid'      => self::TEST_ID,
                'oxshopid'  => '1',
                'oxuserid'  => '_testuser',
                'oemtcount' => $count,
            ]
        );

        return (string) $tracker->save();
    }
}
