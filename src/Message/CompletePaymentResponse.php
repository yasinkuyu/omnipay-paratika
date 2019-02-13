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

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Exception\InvalidResponseException;

class CompletePaymentResponse extends AbstractResponse
{

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;

        if (!$this->checkSession()) {
            throw new InvalidResponseException('GEÇERSİZ SESSION TOKEN');
        }
    }

    public function isSuccessful() {
        if (isset($this->data["responseCode"])) {
            return (string) $this->data["responseCode"] === '00' || $this->data["responseMsg"] === 'Approved';
        }
        return false;
    }

    public function getMessage()
    {

        if( isset($this->data['errorCode']) && $this->data['errorCode'] == "" ){
            return $this->getErrors()[$this->data["errorCode"]];
        }

        if( isset($this->data['errorMsg']) && $this->data['errorMsg'] == "" ){
            return $this->getErrors()[$this->data["errorCode"]];
        }

        return $this->data['errorMsg'];
    }

    public function getTransactionId()
    {
        return $this->data['pgTranId'];
    }

    public function getTransactionReference()
    {
        return $this->data['pgTranRefId'];
    }

    public function getAmount()
    {
        return $this->data['amount'];
    }

    public function getCurrency()
    {
        return $this->data['currency'];
    }

    public function getOrderId()
    {
        return $this->data['pgOrderId'];
    }

    public function getSessionToken()
    {
        return $this->data['sessionToken'];
    }

    private function checkSession()
    {
         return true;
    }
}
