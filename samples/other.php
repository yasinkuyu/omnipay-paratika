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
$gateway->setBank('ISBANK');

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
  
    

    // ======== Void ======== 
    $options = [
        'transactionId' => '17159RzSE07011063',  
    ];

    // Void İptal
    $response = $gateway->void($options)->send();

    if ($response->isSuccessful()) {
        // doğrulama için sağlayıcının 3d kapısına yönlendiriyor
        echo "İşlem iptal edildi";
    } else {
        // işlem gerçekleşmedi
        echo $response->getMessage();
    }

    

    // ======== Refund ======== 
    $options = [
        'currency' => 'TRY',
        'amount'   => '19.86',
        'transactionId' => '17159R4CF07011070',  
    ];

    // İade (Refund)
    $response = $gateway->refund($options)->send();

    if ($response->isSuccessful()) {
        echo "Ödeme iade edildi";
    } else {
        echo $response->getMessage();
    }

    

    // ======== PreAuth ======== 
    $options = [
        'currency' => 'TRY',
        'amount'   => '19.86',
        'transactionId' => '17159R4CF07011070',
        'orderId'     => 'S-123456792',
        'card'          => $card, 
    ];

    // Provizyon aç (PreAuth) (Ödeme bloke) 
    // Provizyonu tamamlamak için purchase.php ve callback.php dosyasını iceleyiniz.
    // Oturum açma ve api işlemleri aynı şekilde çalıştırılmalı.
    // $response = $gateway->completeAuthorize()->send();
    // Bu işlem yalnızca test için
    $response = $gateway->authorize($options)->send();

    if ($response->isRedirect()) {
        $response->redirect();
    } else {
        echo $response->getMessage();
    }
 



    // ======== PostAuth ======== 
    $options = [
        'currency' => 'TRY',
        'amount'   => '19.86',
        'transactionId' => '17159R4CF07011070',
        'orderId'     => 'S-123456792',
        'card'          => $card, 
    ];

    // Provizyon kapat (PostAuth) (Ödeme blokeyi satışa çevir)
    // Provizyon kapatma işlemini için purchase.php dosyasını iceleyin.
    // Oturum açma ve api işlemleri aynı şekilde çalıştırılmalı.
    // Bu yalnızca test için
    $response = $gateway->capture($options)->send();

    if ($response->isRedirect()) {
        $response->redirect();
    } else {
        echo $response->getMessage();
    }
 
 
} catch (\Exception $e) {
    echo $e->getMessage();
}

