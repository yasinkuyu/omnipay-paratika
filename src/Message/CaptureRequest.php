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

class CaptureRequest extends AbstractPayment //AbstractTransaction
{
    public function getData()
    {
    	
        $this->actionType = 'POSTAUTH';
        return parent::getData();
    }
    
}