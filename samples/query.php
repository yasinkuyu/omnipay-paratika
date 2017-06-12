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

try {
 
    $options = [
        'orderId'       => 'S-12345223',
        'returnUrl'     => 'http://local.desktop/Paratika/callback.php',
        'cancelUrl'     => 'http://local.desktop/Paratika/callback.php',
        'queryAction'   => 'QUERYINSTALLMENT',
        //'queryAction'   => 'QUERYCARD',
    ];
 
    // Sorgulama
    // Ek parametreler
    // https://entegrasyon.paratika.com.tr/paratika/api/v2/doc#query
    $response = $gateway->query($options)->send();

    if ($response->isSuccessful()) {

        $details = $response->getQueryDetails();

        echo "İşlem: ". $details["responseMsg"];

/*

print_r($details);

Örnek Çıktı;

Array
(
    [merchantPaymentId] => S-12345223
    [responseCode] => 00
    [responseMsg] => Approved
    [transactionCount] => 1
    [totalTransactionCount] => 0
    [transactionList] => Array
        (
            [0] => Array
                (
                    [pgTranTraceAudit] => 716100000003
                    [pgTranReturnCode] => 00
                    [pgOrderId] => S-12345223
                    [pgTranApprCode] => P85771
                    [pgTranId] => 17161DWJD07012770
                    [pgTranRefId] => 716100000003
                    [timePsSent] => 2017-06-10 03:22:17.001
                    [timePsReceived] => 2017-06-10 03:22:20.075
                    [timeCreated] => 2017-06-10 03:22:16.877
                    [amount] => 19.86
                    [transactionStatus] => AP
                    [currency] => TRY
                    [paymentSystem] => ISBANK
                    [panLast4] => 4509
                    [transactionType] => SALE
                    [installmentCount] => 1
                    [cardOwnerMasked] => I**** B******
                    [customerId] => Musteri-Insya Bilisim
                    [bin] => 450803
                )

        )

)
*/

    } else {
        echo $response->getMessage();
    }
 
 
} catch (\Exception $e) {
    echo $e->getMessage();
}

