<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Model;

/**
 * @eshopExtension
 *
 * This is an example for a module extension (chain extend) of
 * the shop user model.
 * NOTE: class must not be final.
 *
 * @mixin \OxidEsales\Eshop\Application\Model\User
 */
class User extends User_parent
{
    public const OEMT_USER_GREETING_FIELD = 'oemtgreeting';

    public function getPersonalGreeting(): string
    {
        return (string)$this->getRawFieldData(self::OEMT_USER_GREETING_FIELD);
    }

    //NOTE: we only assign the value to the model.
    //Calling save() method will then store it in the database
    public function setPersonalGreeting(string $personalGreeting): void
    {
        $this->assign([
            self::OEMT_USER_GREETING_FIELD => $personalGreeting,
        ]);
    }
}
