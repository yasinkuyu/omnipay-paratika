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

class TransactionResponse extends AbstractResponse
{

    public function __construct(RequestInterface $request, $data) {

        $this->request = $request;

        try {
            $this->data = $data;
        } catch (\Exception $ex) {
            throw new InvalidResponseException();
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

        if( $this->data['errorMsg'] == "" ){
            return $this->getErrors()[$this->data["errorCode"]];
        }

        return $this->data['errorMsg'];
    }

    public function getQueryDetails()
    {
        return $this->data;
    }

    public function getTransactionId()
    {
        return $this->data['pgTranId'];
    }

    public function getTransactionReference()
    {
        return $this->data['pgTranRefId'];
    }

    public function getSessionToken(){
        return $this->data['sessionToken'];
    }

}
