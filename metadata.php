<?php

/**
 * Copyright © Ginger. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id' => 'gingerpayments',
    'title' => [
        'de' => 'Ginger Payment',
        'en' => 'Ginger Payment',
        'fr' => 'Ginger Payment'
    ],
    'description'   => [
        'de' => 'Ginger Payments solution DE',
        'en' =>'Ginger Payments solution EN',
        'fr' => 'Ginger Payments solution FR',
        'nl' => 'Ginger Payments solution NL',
    ],
    'thumbnail' => 'pictures/logo.png',
    'version' => '1.0.0',
    'author' => 'Ginger Payments',
    'url' => 'https://merchant.dev.gingerpayments.com/',
    'email' => 'max.prokofiev@leasoft.org',
    'extend' => [
//        \OxidEsales\Eshop\Application\Model\Order::class => \GingerPayments\Payments\Order\ModuleOrder::class,
//        \OxidEsales\Eshop\Application\Controller\PaymentController::class => \GingerPayments\Payments\Controller\ModulePaymentController::class,
//        \OxidEsales\Eshop\Application\Model\PaymentGateway::class => \GingerPayments\Payments\Model\PaymentGateway::class,
    ],
    'templates' => [
        'ginger_payments_module_settings.tpl' => 'ginger_payments_module/views/admin/ginger_payments_module_settings.tpl',
    ],
    'blocks' => array(
        array(
            'template' => 'page/checkout/payment.tpl',
            'block' => 'select_payment',
            'file' => '/views/blocks/page/checkout/gingerpayments.tpl'
        ),
    ),
    'controllers' => [
        'ModuleSettings' => \GingerPayments\Payments\Controller\ModuleSettingsController::class,
    ],
    'events' => [
        'onActivate' => '\GingerPayments\Payments\Core\ModuleEvents::onActivate',
        'onDeactivate' => '\GingerPayments\Payments\Core\ModuleEvents::onDeactivate'
    ],
    'settings' => [
        /** Main */
        [
            'group' => 'GINGERPAYMENTS_MAIN',
            'name' => 'GINGERPAYMENTS_APIKEY',
            'type' => 'str',
            'value' => ''
        ],
    ],
];
