<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Core;

use Exception;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class defines what module does on Shop events.
 *
 * @codeCoverageIgnore
 */
final class ModuleEvents
{
//**
//* Execute action on activate event.
//*
//* @return void
//*/
    public static function onActivate(): void
    {
        self::addGingerpaymentsPaymentMethods();
    }
    public static function onDeactivate(): void
    {

    }

    /**
     * Adds ginger payment methods
     *
     */
    public static function addGingerpaymentsPaymentMethods(): void
    {
    }
}
