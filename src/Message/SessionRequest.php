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

class SessionRequest extends AbstractPayment
{

	// Oturum aç
    public function getData()
    {
    	
        $this->actionType = 'SESSIONTOKEN';
        return parent::getData();
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

        $httpResponse = $this->httpClient->post($this->getEndpoints()["api"], $headers, $data)->send();

        return $this->response = new TransactionResponse($this, $httpResponse->json());
    }
 

}
