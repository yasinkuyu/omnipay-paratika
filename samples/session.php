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

$gateway->setBin("435508");
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

$gateway->setMode("api"); 

$card = [
    'number'        => '4355084355084358',
    'expiryMonth'   => '12',
    'expiryYear'    => '2030',
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
        'installment'   => 0,
        'orderId'       => 'S-123413006', // Benzersiz olmalı
        'returnUrl'     => 'http://local.desktop/Paratika/callback.php',
        'cancelUrl'     => 'http://local.desktop/Paratika/callback.php',
        'sessionType'   => 'PAYMENTSESSION', //PAYMENTSESSION WALLETSESSION
        'card'          => $card,
    ];
 
    // Oturum 
    $response = $gateway->session($options)->send();

    if ($response->isSuccessful()) {
        echo "İşlem başarılı: ". $response->getSessionToken(); 
    } elseif ($response->isRedirect()) {
        $response->redirect();
    } else {
        echo $response->getMessage();
    }
 
 
} catch (\Exception $e) {
    echo $e->getMessage();
}

