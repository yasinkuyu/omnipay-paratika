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

class CompletePaymentRequest extends AbstractPayment //AbstractTransaction //AbstractPayment
{

	public function getData()
    {
        return $this->httpRequest->request->all();
    }

    public function sendData($data)
    {
        return $this->response = new CompletePaymentResponse($this, $data);
    }

}