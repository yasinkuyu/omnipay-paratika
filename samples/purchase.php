<?php

/*
Omnipay-Paratika

Paratika (Asseco) MOTO/3D gateway for Omnipay payment processing library

İnsya Bilişim Teknolojileri
http://insya.com

@yasinkuyu
07.06.2017
*/

require __DIR__ . '/vendor/autoload.php';

use Omnipay\Omnipay;

$gateway = Omnipay::create('Paratika');
$gateway->setMerchant('10000000');
$gateway->setMerchantUser('test@yasinkuyu.net');
$gateway->setMerchantPassword('Paratika123');
$gateway->setSecretKey('QOClasdJUuDDWasdasdasd');

$gateway->setBin("545616");
$gateway->setMode("api");
try {
    $cardNetwork = 'UNKNOWN';
    $options = [
        'queryAction'   => 'QUERYBIN'
    ];
 
    $response = $gateway->query($options)->send();
    if ($response->isSuccessful()) {
        $details = $response->getQueryDetails();
        if($details['responseCode'] = "00"){
            $cardNetwork = $details['bin']['cardNetwork'];
        }
        
    } else {
        exit($response->getMessage());
    }
 
 
} catch (\Exception $e) {
    echo $e->getMessage();
}
$gateway->setBank($cardNetwork);

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

