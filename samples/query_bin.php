<?php

require __DIR__ . '/vendor/autoload.php';

use Omnipay\Omnipay;

$gateway = Omnipay::create('Paratika');
$gateway->setMerchant('10000511');
$gateway->setMerchantUser('info@biletalsat.com');
$gateway->setMerchantPassword('6687Onur**123');
$gateway->setSecretKey('G3Hq9XO0tfCEnFnpuOAB');
 
$gateway->setBin("526911");
$gateway->setMode("api");

try {
 
    $options = [
        'queryAction'   => 'QUERYBIN'
    ];
 
    $response = $gateway->query($options)->send();

    if ($response->isSuccessful()) {

        $details = $response->getQueryDetails();
        print_r($details);

    } else {
        echo $response->getMessage();
    }
 
 
} catch (\Exception $e) {
    echo $e->getMessage();
}

