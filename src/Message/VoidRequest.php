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

class VoidRequest extends AbstractTransaction
{

	// İptal işlemi
    public function getData()
    {
    	
        $this->actionType = 'VOID';
        return parent::getData();
    }

}
