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

class QueryRequest extends AbstractTransaction
{

	// Sorgulama işlemi
    public function getData()
    {

        $this->actionType = 'QUERYTRANSACTION';

        if($this->getQueryAction()){
        	$this->actionType = $this->getQueryAction();;
        }

        return parent::getData();
    }

}
