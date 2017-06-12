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

class AuthorizeRequest extends AbstractPayment
{
    public function getData()
    {
    	// Ödeme blokaj
        $this->actionType = 'PREAUTH';
        return parent::getData();
    }
    
}