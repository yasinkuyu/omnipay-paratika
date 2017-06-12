<?php

/*
Omnipay-Paratika

Paratika (Asseco) MOTO/3D gateway for Omnipay payment processing library

İnsya Bilişim Teknolojileri
http://insya.com

@yasinkuyu
07.06.2017
*/

namespace Omnipay\Paratika\Message;

abstract class AbstractTransaction extends AbstractRequest
{

    protected $actionType;
 
    public function getData()
    {
 
        $data['ACTION']                 = $this->actionType;
        $data['MERCHANTUSER']           = $this->getMerchantUser();
        $data['MERCHANTPASSWORD']       = $this->getMerchantPassword();
        $data['MERCHANT']               = $this->getMerchant();
        $data['PGTRANID']               = $this->getTransactionId();
        $data['MERCHANTPAYMENTID']      = $this->getOrderId();

        return $data;
    }

    public function sendData($data)
    {

        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded'
        );

        $this->httpClient->setConfig(array(
            'curl.options' => array(
                'CURLOPT_SSL_VERIFYHOST' => 2,
                'CURLOPT_SSLVERSION' => 0,
                'CURLOPT_SSL_VERIFYPEER' => 0,
                'CURLOPT_RETURNTRANSFER' => 1,
                'CURLOPT_POST' => 1
            )
        ));

        $httpResponse = $this->httpClient->post($this->getEndpoint(), $headers, $data)->send();

        return $this->response = new TransactionResponse($this, $httpResponse->json());
    }
 
}
