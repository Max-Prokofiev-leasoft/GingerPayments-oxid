<?php

namespace GingerPayments\Payments\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use Ginger\ApiClient;
use GingerPluginSdk\Client;
use GingerPluginSdk\Collections\AdditionalAddresses;
use GingerPluginSdk\Collections\Transactions;
use GingerPluginSdk\Entities\Address;
use GingerPluginSdk\Entities\Customer;
use GingerPluginSdk\Entities\Order;
use GingerPluginSdk\Entities\Transaction;
use GingerPluginSdk\Exceptions\APIException;
use GingerPluginSdk\Properties\Amount;
use GingerPluginSdk\Properties\ClientOptions;
use GingerPluginSdk\Properties\Currency;
use GingerPluginSdk\Properties\EmailAddress;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\EshopCommunity\Application\Model\PaymentGateway as BasePaymentGateway;


class PaymentGateway extends BasePaymentGateway
{

    /**
     * Sets payment parameters.
     *
     * @param object $oUserpayment User payment object
     */
    public function setPaymentParams($oUserpayment)
    {
        // store data
        $this->_oPaymentInfo = &$oUserpayment;
    }

    /**
     * @throws APIException
     */
    public function executePayment($dAmount, &$oOrder)
    {
        $this->_iLastErrorNo = null;
        $this->_sLastError = null;

//        if (!$this->isActive()) {
//            return true; // fake yes
//        }

        // proceed with no payment
        // used for other countries
//        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);

        if (@$this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value === 'gingerpaymentscreditcard') {
            //initialize variables
            $oUser = $this->_oPaymentInfo->getUser();

            //API
            $endpoint = "https://api.dev.gingerpayments.com";
            $apiKey = "e9a320bc591c41668c89e5ba59591c53" ;
            $clientOptions = new ClientOptions(endpoint: $endpoint, useBundle: true,apiKey: $apiKey);
            $gingerApi = new Client(options: $clientOptions);

            //Currency
            $currencyArr[] = $oOrder->getOrderCurrency();
            $currency = $currencyArr[0]->name;
            $currency = new Currency($currency);

            //Amount
            $amountOrder = (int)($dAmount * 10);
            $amount = new Amount(value: $amountOrder);

            //Transaction
            $entityTransaction = new Transaction(paymentMethod: 'credit-card');
            $transaction = new Transactions(Transaction: $entityTransaction);

            //Customer
            $postalCode = $oUser->oxuser__oxzip->value;
            $countryId = $oUser->oxuser__oxcountryid->value;
            $oxCountry = oxNew(Country::class);
            $oxCountry->load($countryId);
            $countryIso = $oxCountry->oxcountry__oxisoalpha2->value;

            $country = new \GingerPluginSdk\Properties\Country($countryIso);


            $address = new Address(addressType: 'customer',postalCode:$postalCode,country: $country);

            $additionalAddress = new AdditionalAddresses($address);

            $firstName = $oUser->oxuser__oxfname->value;
            $lastName = $oUser->oxuser__oxlname->value;
            $oxEmailAddress = $oUser->oxuser__oxusername->value;
            $emailAddress = new EmailAddress($oxEmailAddress);
            $customer = new Customer(additionalAddresses: $additionalAddress,firstName: $firstName,lastName: $lastName,emailAddress: $emailAddress );

            //Order
            $order = new Order(currency: $currency,amount: $amount,transactions: $transaction,customer:$customer );
            $gingerApi->sendOrder($order);
            $payment_url = $gingerApi->sendOrder($order)->getPaymentUrl();

            header("Location: $payment_url");
            exit();

        }

        return false;
    }


}