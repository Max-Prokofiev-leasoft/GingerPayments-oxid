<?php

namespace GingerPayments\Payments\Builders;

use GingerPluginSdk\Collections\AdditionalAddresses;
use GingerPluginSdk\Entities\Customer;
use GingerPluginSdk\Entities\Address;
use GingerPluginSdk\Properties\EmailAddress;

class CustomerBuilder
{
    public static function buildCustomer($order): Customer
    {
        // Build customer entity from order data
        $oUser = $order->getUser();
        $address = new Address(
            'customer',
            $oUser->oxuser__oxzip->value,
            new \GingerPluginSdk\Properties\Country(self::getCountryIso($oUser))
        );

        return new Customer(
            new AdditionalAddresses(addresses: $address),
            $oUser->oxuser__oxfname->value,
            $oUser->oxuser__oxlname->value,
            new EmailAddress(value: $oUser->oxuser__oxusername->value)
        );
    }

    protected static function getCountryIso($oUser)
    {
        // Get country ISO code from user data
        $country = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
        $country->load($oUser->oxuser__oxcountryid->value);
        return $country->oxcountry__oxisoalpha2->value;
    }
}
