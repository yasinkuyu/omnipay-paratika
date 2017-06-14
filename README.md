# Omnipay: Paratika

**Paratika (Asseco) (Akbank, TEB, Halkbank, Finansbank, İş Bankası, Şekerbank, Vakıfbank ) gateway for Omnipay payment processing library**

[![Latest Stable Version](https://poser.pugx.org/yasinkuyu/omnipay-paratika/v/stable)](https://packagist.org/packages/yasinkuyu/omnipay-paratika) 
[![Total Downloads](https://poser.pugx.org/yasinkuyu/omnipay-paratika/downloads)](https://packagist.org/packages/yasinkuyu/omnipay-paratika) 
[![Latest Unstable Version](https://poser.pugx.org/yasinkuyu/omnipay-paratika/v/unstable)](https://packagist.org/packages/yasinkuyu/omnipay-paratika) 
[![License](https://poser.pugx.org/yasinkuyu/omnipay-paratika/license)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Paratika (Turkish Payment Gateways) support for Omnipay.


Paratika Api Entegrasyonu, Asseco firmasının yeni sanal pos hizmeti için Omnipay kütüphanesi. 
Akbank, TEB, Halkbank, Finansbank, İş Bankası, Şekerbank ve Vakıfbank taksit imkanı sunuyor. 


## Installation

    composer require yasinkuyu/omnipay-paratika:~2.0

## Basic Usage

The following gateways are provided by this package:

* Paratika
    - Akbank
    - TEB
    - Hakbank 
    - Finansbank
    - İş Bankası 
    - Şekerbank
    - Vakıfbank 

Gateway Methods

* authorize($options) - authorize an amount on the customer's card
* capture($options) - capture an amount you have previously authorized
* purchase($options) - authorize and immediately capture an amount on the customer's card
* refund($options) - refund an already processed transaction
* void($options) - generally can only be called up to 24 hours after submitting a transaction
* session($options) - session parameters required to purchase.
* query($options) - query for various other inquiries.

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.
 
## Samples

			require __DIR__ . '/vendor/autoload.php';

			use Omnipay\Omnipay;

			$gateway = Omnipay::create('Paratika');
			$gateway->setMerchant('10000000');
			$gateway->setMerchantUser('test@yasinkuyu.net');
			$gateway->setMerchantPassword('Paratika123');
			$gateway->setSecretKey('QOClasdJUuDDWasdasdasd');
			$gateway->setBank('ISBANK');

			$gateway->setMode("NonDirectPost3D");
			//Diğer paremetreler: api DirectPost3D NonDirectPost3D
			//3D test için işlem şifresi a ya da 1

			// Zorunlu parametreler
			$card = [
			    'number'        => '5456165456165454',
			    'expiryMonth'   => '12',
			    'expiryYear'    => '2020',
			    'cvv'           => '000',

			    'email'         => 'info@insya.com',
			    'firstname'     => 'Insya',
			    'lastname'      => 'Bilisim',
			    'phone'         => '95555050505',

			    'billingAddress1' => 'Test sokak',
			    'billingCity'     => 'Tekirdag',
			    'billingPostcode' => '59850',
			    'billingCountry'  => 'Turkey',

			    'shippingAddress1' => 'Test sokak',
			    'shippingCity'     => 'Tekirdag',
			    'shippingPostcode' => '59850',
			    'shippingCountry'  => 'Turkey'
			];

			try {
			 
			    $options = [
			        'amount'        => 100.00,
			        'currency'      => 'TRY',
			        //'installment'   => 0, // Taksit
			        'orderId'       => 'S-12341308', // Benzersiz olmalı.
			        'returnUrl'     => 'http://local.desktop/Paratika/callback.php',
			        'cancelUrl'     => 'http://local.desktop/Paratika/callback.php',
			        'sessionType'   => 'PAYMENTSESSION', //Diğer parametreler: PAYMENTSESSION WALLETSESSION
			        'card'          => $card,
			    ];

			    // SessionToken almak için oturum açalım
			    $sessionResponse = $gateway->session($options)->send();
			    
			    if ($sessionResponse->isSuccessful()) {
			        
			            $sessionToken =  $sessionResponse->getSessionToken();

			            // Oturum değiştikenini satış ve diğer işlemlerde kullanmak için tanımlayalım.
			            $gateway->setSessionToken($sessionToken);

			            // Auth (Satış) işlemi
			            $response = $gateway->purchase($options)->send();

			            if ($response->isSuccessful()) {
			                echo "İşlem başarılı transactionId:". $response->getTransactionId();
			            } elseif ($response->isRedirect()) {
			                $response->redirect();
			            } else {
			                echo $response->getMessage();
			            }

			    } elseif ($sessionResponse->isRedirect()) {
			        $sessionResponse->redirect();
			    } else {
			        echo $sessionResponse->getMessage();
			    }

			 
			} catch (\Exception $e) {
			    echo $e->getMessage();
			}

## NestPay (EST)
(İş Bankası, Akbank, Finansbank, Denizbank, Kuveytturk, Halkbank, Anadolubank, ING Bank, Citibank, Cardplus) gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-nestpay

## Posnet
Posnet (Yapı Kredi, Vakıfbank, Anadolubank) gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-posnet

## İyzico
Iyzico gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-iyzico

## GVP (Granti Sanal Pos)
Gvp (Garanti, Denizbank, TEB, ING, Şekerbank, TFKB) gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-gvp

## BKM Express
BKM Express gateway for Omnipay payment processing library
https://github.com/yasinkuyu/omnipay-bkm


## Composer Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "yasinkuyu/omnipay-paratika": "~2.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update


## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/yasinkuyu/omnipay-paratika/issues),
or better yet, fork the library and submit a pull request.

## Roadmap
 
