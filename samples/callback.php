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

try {

    // Complete (Satış sonucu)
    $response = $gateway->completePurchase()->send();

    // Provizyon  (Complate PreAuth) (Ödeme bloke)  (3d)
    //$response = $gateway->completeAuthorize()->send();
    
    if ($response->isSuccessful()) {
    	echo "İşlem başarılı";
    } else {
        // satış başarısız
        echo $response->getMessage();
    }


} catch (\Exception $e) {
    echo $e->getMessage();
}
 

echo "<br><a href='purchase.php'>Tekrar dene</a>";