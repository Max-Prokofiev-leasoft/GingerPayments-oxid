<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Tests\Codeception\Helper;

use OxidEsales\Codeception\Module\Translation\Translator;
use GingerPayments\Payments\Tests\Codeception\AcceptanceTester;

/**
 * @group modules_GingerPayments-oxid
 * @group modules_GingerPayments-oxid_startpage
 */
final class GreetingCest
{
    public function _before(AcceptanceTester $I): void
    {
        //ensure each test start from same environment
        $I->setGreetingModeGeneric();
    }

    public function _after(AcceptanceTester $I): void
    {
        //clean up after each test
        $I->setGreetingModeGeneric();
        $this->setUserPersonalGreeting($I, '');
    }

    public function testGreetingModeGeneric(AcceptanceTester $I): void
    {
        $I->wantToTest('generic greeting on start page. No logged in user.');

        $I->openShop();

        $I->waitForText(Translator::translate('OEMODULETEMPLATE_GREETING'));
        $I->see(Translator::translate('OEMODULETEMPLATE_GREETING_GENERIC'));
        $I->dontSeeElement('#oemt_update_greeting');
    }

    public function testGreetingModeGenericWithUser(AcceptanceTester $I): void
    {
        $I->wantToTest('personal greeting on start page with logged in user');

        $this->setUserPersonalGreeting($I, 'Hi there sweetie');

        $I->openShop()
            ->loginUser($I->getDemoUserName(), $I->getDemoUserPassword());

        $I->waitForText(Translator::translate('OEMODULETEMPLATE_GREETING'));
        $I->see(Translator::translate('OEMODULETEMPLATE_GREETING_GENERIC'));
        $I->dontSee('Hi there sweetie'); //no personal greeting even if user has one set
        $I->dontSeeElement('#oemt_update_greeting');
    }

    public function testGreetingModePersonalWithoutUser(AcceptanceTester $I): void
    {
        $I->wantToTest('personal greeting on start page without logged in user');

        $I->setGreetingModePersonal();
        $I->openShop();

        $I->waitForText(Translator::translate('OEMODULETEMPLATE_GREETING'));
        $I->dontSee(Translator::translate('OEMODULETEMPLATE_GREETING_GENERIC'));
        $I->dontSeeElement('#oemt_update_greeting');
    }

    public function testGreetingModePersonalUser(AcceptanceTester $I): void
    {
        $I->wantToTest('personal greeting on start page with logged in user who has personal greeting');

        $I->setGreetingModePersonal();
        $this->setUserPersonalGreeting($I, 'Hi there sweetie');

        $I->openShop()
            ->loginUser($I->getDemoUserName(), $I->getDemoUserPassword());

        $I->waitForText(Translator::translate('OEMODULETEMPLATE_GREETING'));
        $I->dontSee(Translator::translate('OEMODULETEMPLATE_GREETING_GENERIC'));
        $I->see('Hi there sweetie');
        $I->seeElement('#oemt_update_greeting');
    }

    private function setUserPersonalGreeting(AcceptanceTester $I, string $value = ''): void
    {
        $I->updateInDatabase(
            'oxuser',
            [
                'oemtgreeting' => $value,
            ],
            [
                'oxusername' => $I->getDemoUserName(),
            ]
        );
    }
}
