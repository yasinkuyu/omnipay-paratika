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

use Omnipay\Common\Exception\InvalidCreditCardException;
 
abstract class AbstractPayment extends AbstractRequest
{
    protected $actionType;

    protected $allowedCardBrands = [
        'visa' => 1,
        'mastercard' => 2
    ];

    protected $allowedBanks = [
        'AKBANK'    => 'AXESS',
        'TEB'       => 'BONUS',
        'HALKBANK'  => 'PARAF',
        'FINANSBANK'=> 'CARD_FNS',
        'ISBANK'    => 'MAXIMUM',
        'SEKERBANK' => 'BONUS',
        'VAKIFBANK' => 'WORLD',
    ];

    protected $allowedCurrencies = [
        "CHF", "MXN", "ARS", "SAR", 
        "ZAR", "INR", "CNY", "AUD", 
        "ILS", "JPY", "PLN", "GBP", 
        "BOB", "IDR", "HUF", "KWD", 
        "TRY", "RUB", "AED", "RSD", 
        "EUR", "DKK", "COP", "USD", 
        "CAD", "BGN", "NOK", "RON", 
        "CZK", "SEK", "NZD", "BRL", 
        "BHD"
    ];
 
    public function getData()
    {

        $this->validate('amount', 'card');

        $cardBrand = $this->getCard()->getBrand();

        if (!array_key_exists($cardBrand, $this->allowedCardBrands)) {
            throw new InvalidCreditCardException('Sadece Visa ya da MasterCard kullanılabilir.');
        }
 
        if (!array_key_exists($this->getBank(), $this->allowedBanks)) {
            throw new InvalidCreditCardException('Geçersiz vPos/Banka.');
        }

        if (!in_array($this->getCurrency(), $this->allowedCurrencies)) {
            throw new InvalidCreditCardException('Geçersiz para birimi.');
        }

        $data = array();

        // Api bilgileri
        $data['ACTION']                 = $this->actionType;
        $data['MERCHANTUSER']           = $this->getMerchantUser();
        $data['MERCHANTPASSWORD']       = $this->getMerchantPassword();
        $data['MERCHANT']               = $this->getMerchant();

        // Sipariş bilgileri
        $data['MERCHANTPAYMENTID']      = $this->getOrderId();
        $data['AMOUNT']                 = $this->getAmount();
        $data['CURRENCY']               = $this->getCurrency();

        // Müşteri bilgileri
        $data['CUSTOMER']               = "Musteri-". $this->getCard()->getFirstName() . " " . $this->getCard()->getLastName();
        $data['CUSTOMERNAME']           = $this->getCard()->getFirstName() . " " . $this->getCard()->getLastName();
        $data['CUSTOMEREMAIL']          = $this->getCard()->getEmail();
        $data['CUSTOMERIP']             = $_SERVER['REMOTE_ADDR'];  
        $data['CUSTOMERUSERAGENT']      = $_SERVER['HTTP_USER_AGENT'];
        $data['NAMEONCARD']             = $this->getCard()->getFirstName() . " " . $this->getCard()->getLastName();
        $data['CUSTOMERPHONE']          = $this->getCard()->getBillingPhone();

        // Fatura bilgileri
        $data['BILLTOADDRESSLINE']      = $this->getCard()->getBillingAddress1() . ' '. $this->getCard()->getBillingAddress2();
        $data['BILLTOCITY']             = $this->getCard()->getBillingCity();
        $data['BILLTOCOUNTRY']          = $this->getCard()->getBillingCountry();
        $data['BILLTOPOSTALCODE']       = $this->getCard()->getBillingPostcode();
        $data['BILLTOPHONE']            = $this->getCard()->getBillingPhone();

        // Nakliye bilgileri
        $data['SHIPTOADDRESSLINE']      = $this->getCard()->getShippingAddress1() . ' '. $this->getCard()->getShippingAddress2();
        $data['SHIPTOCITY']             = $this->getCard()->getShippingCity();
        $data['SHIPTOCOUNTRY']          = $this->getCard()->getShippingCountry();
        $data['SHIPTOPOSTALCODE']       = $this->getCard()->getShippingPostcode();
        $data['SHIPTOPHONE']            = $this->getCard()->getShippingPhone();

        // Api ve oturum açmak için
        // Kredi kartı bilgileri
        $data['CARDPAN']                = $this->getCard()->getNumber();
        $data['CARDEXPIRY']             = $this->getCard()->getExpiryDate('m') . '.'. $this->getCard()->getExpiryDate('Y');
        $data['CARDCVV']                = $this->getCard()->getCvv();
        $data['PAYMENTSYSTEM']          = $this->getBank();

        // MOTO/3D için
        // Kredi kartı bilgileri
        $data['cardOwner']              = $this->getCard()->getFirstName() . " " . $this->getCard()->getLastName();
        $data['pan']                    = $this->getCard()->getNumber();
        $data['expiryYear']             = $this->getCard()->getExpiryDate('Y');
        $data['expiryMonth']            = $this->getCard()->getExpiryDate('m');
        $data['cvv']                    = $this->getCard()->getCvv();

        if($this->getSessionType() != "") {

            $data['RETURNURL']          = $this->getReturnUrl();
            $data['SESSIONTYPE']        = $this->getSessionType();
            $data['ORDERITEMS']         = urlencode(json_encode(array(
                array(
                    'code' => 'Test001',
                    'name' => 'Ürün adı',
                    'description' => 'Açıklama',
                    'quantity' => 1,
                    'amount' => 100.00
                )
            )));
        }

        // Taksit
        if ($this->getInstallment() != "") {

            $data['INSTALLMENTS'] = $this->getInstallment(); 

            $data['installmentCount'] = $this->getInstallment(); 
        }

        /*
         Paratika için hash parametreleri

         HASH = HEX ( SHA-256 ( CONCAT ( [ACTION], [MERCHANT], [CUSTOMER], 
                [MERCHANTPAYMENTID], [MERCHANTSECRETKEY], [RANDOM] ) ) )
        */

        $hash = $data['ACTION'] .
                $data['MERCHANT'] .
                $data['CUSTOMER'] .
                $data['MERCHANTPAYMENTID'] .
                $this->getSecretKey() .
                uniqid();

        $hash = hash("sha256", $hash); // SHA-256
        $hash = base64_encode(pack('H*', $hash)); // HEX

        $data['HASH'] = $hash;

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new PaymentResponse($this, $data);
    }

}
