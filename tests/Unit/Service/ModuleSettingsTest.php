<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\modules\oe\moduletemplate\tests\Unit\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingService;
use GingerPayments\Payments\Core\Module;
use GingerPayments\Payments\Service\ModuleSettings;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\UnicodeString;

final class ModuleSettingsTest extends TestCase
{
    /**
     * @dataProvider getGreetingModeDataProvider
     */
    public function testGetGreetingMode(string $value, string $expected): void
    {
        $mssMock = $this->createPartialMock(ModuleSettingService::class, ['getString']);
        $mssMock->method('getString')->willReturnMap([
            [ModuleSettings::GREETING_MODE, Module::MODULE_ID, new UnicodeString($value)]
        ]);

        $sut = new ModuleSettings($mssMock);
        $this->assertSame($expected, $sut->getGreetingMode());
    }

    public function getGreetingModeDataProvider(): array
    {
        return [
            [
                'value' => '',
                'expected' => ModuleSettings::GREETING_MODE_GENERIC
            ],
            [
                'value' => 'someUnpredictable',
                'expected' => ModuleSettings::GREETING_MODE_GENERIC
            ],
            [
                'value' => ModuleSettings::GREETING_MODE_GENERIC,
                'expected' => ModuleSettings::GREETING_MODE_GENERIC
            ],
            [
                'value' => ModuleSettings::GREETING_MODE_PERSONAL,
                'expected' => ModuleSettings::GREETING_MODE_PERSONAL
            ],
        ];
    }

    /**
     * @dataProvider isPersonalGreetingModeDataProvider
     */
    public function testIsPersonalGreetingMode(string $value, bool $expected): void
    {
        $mssMock = $this->createPartialMock(ModuleSettingService::class, ['getString']);
        $mssMock->method('getString')->willReturnMap([
            [ModuleSettings::GREETING_MODE, Module::MODULE_ID, new UnicodeString($value)]
        ]);

        $sut = new ModuleSettings($mssMock);
        $this->assertSame($expected, $sut->isPersonalGreetingMode());
    }

    public function isPersonalGreetingModeDataProvider(): array
    {
        return [
            [
                'value' => ModuleSettings::GREETING_MODE_GENERIC,
                'expected' => false
            ],
            [
                'value' => ModuleSettings::GREETING_MODE_PERSONAL,
                'expected' => true
            ],
        ];
    }

    public function testSaveGreetingMode(): void
    {
        $value = 'someValue';

        $mssMock = $this->createPartialMock(ModuleSettingService::class, ['saveString']);
        $mssMock->expects($this->atLeastOnce())->method('saveString')->with(
            ModuleSettings::GREETING_MODE,
            $value,
            Module::MODULE_ID
        );

        $sut = new ModuleSettings($mssMock);
        $sut->saveGreetingMode($value);
    }

    public function testIsLoggingEnabledReturnsTrue(): void
    {
        $mssMock = $this->createPartialMock(ModuleSettingService::class, ['getBoolean']);
        $mssMock->method('getBoolean')->willReturnMap([
            [ModuleSettings::LOGGER_STATUS, Module::MODULE_ID, true]
        ]);

        $sut = new ModuleSettings($mssMock);
        $result = $sut->isLoggingEnabled();

        $this->assertTrue($result);
    }

    public function testIsLoggingEnabledReturnsFalse(): void
    {
        $mssMock = $this->createPartialMock(ModuleSettingService::class, ['getBoolean']);
        $mssMock->method('getBoolean')->willReturnMap([
            [ModuleSettings::LOGGER_STATUS, Module::MODULE_ID, false]
        ]);

        $sut = new ModuleSettings($mssMock);
        $result = $sut->isLoggingEnabled();

        $this->assertFalse($result);
    }
}
