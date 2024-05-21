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
        'de' => 'Ginger Payments',
        'en' => 'Ginger Payments',
        'fr' => 'Ginger Payments'
    ],
    'description' => [
        'de' => 'Ginger Payments solution DE',
        'en' => 'Ginger Payments solution EN',
        'fr' => 'Ginger Payments solution FR',
        'nl' => 'Ginger Payments solution NL',
    ],
    'thumbnail' => 'pictures/logo.png',
    'version' => '1.0.0',
    'author' => 'Ginger Payments',
    'url' => 'https://merchant.dev.gingerpayments.com/',
    'email' => 'max.prokofiev@leasoft.org',
    'extend' => [
        oxpaymentgateway::class => \GingerPayments\Payments\Model\PaymentGateway::class,
        oxorder::class => \GingerPayments\Payments\Model\ModuleOrder::class,
    ],
    'blocks' => [
        [
            'template' => 'page/checkout/payment.tpl',
            'block' => 'select_payment',
            'file' => '/views/blocks/page/checkout/gingerpayments.tpl'
        ],
    ],
    'events' => [
        'onActivate' => '\GingerPayments\Payments\Core\ModuleEvents::onActivate',
        'onDeactivate' => '\GingerPayments\Payments\Core\ModuleEvents::onDeactivate'
    ],
    'settings' => [
        /** Main */
        [
            'group' => 'gingerpayments_main',
            'name' => 'gingerpayment_apikey',
            'type' => 'str',
            'value' => 'Please insert your API key'
        ],
    ],
];
