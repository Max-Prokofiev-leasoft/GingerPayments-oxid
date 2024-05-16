<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace GingerPayments\Payments\Core;

use Exception;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\Eshop\Core\Field;
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
        self::removeGingerpaymentsPaymentMethods();
    }

    /**
     * Adds ginger payment methods
     *
     */
    public static function addGingerpaymentsPaymentMethods(): void
    {
        $aPayments = [
            'gingerpaymentscreditcard' => ['OXID' => 'gingerpaymentscreditcard',
                'OXDESC_DE' => 'Kreditkarte',
                'OXDESC_EN' => 'Credit Card',
                'OXLONGDESC_DE' => 'Der Betrag wird von Ihrer Kreditkarte abgebucht, sobald die Bestellung abgeschickt wird',
                'OXLONGDESC_EN' => 'The amount will be debited from your credit card once the order is submitted'
            ],
            'gingerpaymentssepa' => ['OXID' => 'gingerpaymentssepa',
                'OXDESC_DE' => 'Lastschrift SEPA',
                'OXDESC_EN' => 'Direct Debit SEPA',
                'OXLONGDESC_DE' => 'Ihr Konto wird nach Abschicken der Bestellung belastet',
                'OXLONGDESC_EN' => 'Your account will be debited upon the order submission'
            ]
        ];
        $oLangArray = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageArray();
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        foreach ($oLangArray as $oLang) {
            foreach ($aPayments as $aPayment) {
                $oPayment->setId($aPayment['OXID']);
                $oPayment->setLanguage($oLang->id);
                $sLangAbbr = in_array($oLang->abbr, ['de', 'en']) ? $oLang->abbr : 'en';
                $oPayment->oxpayments__oxid = new Field($aPayment['OXID']);
                $oPayment->oxpayments__oxaddsumrules = new Field('31');
                $oPayment->oxpayments__oxtoamount = new Field('1000000');
                $oPayment->oxpayments__oxtspaymentid = new Field('');
                $oPayment->oxpayments__oxdesc = new Field($aPayment['OXDESC_' . strtoupper($sLangAbbr)]);
                $oPayment->oxpayments__oxlongdesc = new Field($aPayment['OXLONGDESC_' . strtoupper($sLangAbbr)]);
                $oPayment->save();
            }
        }
        unset($oPayment);
    }

    public static function removeGingerpaymentsPaymentMethods(): void
    {
        $aPayments = [
            'gingerpaymentscreditcard',
            'gingerpaymentssepa'
        ];
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        foreach ($aPayments as $sPaymentOxid) {
            if ($oPayment->load($sPaymentOxid)) {
                $oPayment->delete();
            }
        }
        unset($oPayment);
    }

}
