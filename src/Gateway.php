<?php

/*
Omnipay-Paratika

Paratika (Asseco) MOTO/3D gateway for Omnipay payment processing library

İnsya Bilişim Teknolojileri
http://insya.com

@yasinkuyu
07.06.2017

Docs 
https://entegrasyon.paratika.com.tr/paratika/api/v2/doc#introduction


Notlar

API isteklerini doğrulamak için, geleneksel şifre yöntemi yerine, rastgele oluşturulmus bir data ve dinamik olarak oluşturulmuş bir hash değeri parametreleri kullanılacaktır. Bu RANDOM ve HASH parametrelerinin API kullanıcı hesabından API istekleri gönderirken iletilmesi gerekmektedir. Hash değeri üretilirken, gizli anahtar bilgisi de kullanılacaktır. Bu gizli anahtar bilgisini Ayarlar --> Temel Ayarlar menüsünden görüntüleyebilirsiniz. Gizli anahtarın kendisi bir parametre değildir, sadece hash değeri üretilirken kullanılmaktadır.

RANDOM: Sadece rakam ve harflerden oluşan, uzunluğu 8 ve 64 btye arasında değişen, rasstgele (random) üretilen tekil bir değerdir. Bu değerin rastgele üretilmesinde güçlü ve sağlam bir yazılım kullanılmalıdır.
HASH: Küçük/büyük harf duyarlılığı olmayan 32 byte (256 bit) uzunluğundaki hexadecimal değer. Bununla birlikte hash değerinin uzunluğu 64 karaktere çıkmaktadır.

Hash algoritması olarak SHA-256 kullanılacaktır. İşlem , Üye İş Yeri, Müşteri Numarası, Müşteri, Ödeme ID ve Rastgele Değer parametreleri, Gizli Anahatar değeri ile birleştirilir.

ALGORITM: Hash algoritması tanımlanırken kullanılabilecek opsiyonel bir alandır. Default değeri SHA-256'dır. SHA-512, SHA-384, HA-224, MD2 ve MD5 gibi diğer alternatifler kullanılabilir.

Hash değeri oluşturma formulü şu şeklidedir: "HASH = HEX ( SHA-256 ( CONCAT ( [ACTION], [MERCHANT], [CUSTOMER], [MERCHANTPAYMENTID], [MERCHANTSECRETKEY], [RANDOM] ) ) )" Ancak bu parametre seti, kullanılan işlem tipine göre değişiklik gösterebilir. MERCHANTPAYMENTID'nin kullanılmadığı işlem tiplerinde bu alanın hash formülünden de çıkarılması gerekmektedir. Bu konudaki detaylı örnekler için API dokümanına bakınız.
Gerçek örnekler için API dokümantasyonunu inceleyiniz.

*/

namespace Omnipay\Paratika;

use Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Paratika';
    }

    public function getDefaultParameters()
    {
        return array(
            'mode' => '',
            'merchant' => '',
            'merchantuser' => '',
            'merchantpassword' => '',
            'orderid' => '',
            'transactionId' => '',
            'currency' => '',
            'actionType' => 'SALE',
            'queryAction' => 'QUERYTRANSACTION',
            'installment' => 1,
            'testMode' => false,
        );
    }

    public function getMode() {
        return $this->getParameter('mode');
    }

    public function setMode($value) {
        return $this->setParameter('mode', $value);
    }

    public function getBank() {
        return $this->getParameter('bank');
    }

    public function setBank($value) {
        return $this->setParameter('bank', $value);
    }

    public function getMerchant() {
        return $this->getParameter('merchant');
    }

    public function setMerchant($value) {
        return $this->setParameter('merchant', $value);
    }

    public function getMerchantUser() {
        return $this->getParameter('merchantuser');
    }

    public function setMerchantUser ($value) {
        return $this->setParameter('merchantuser', $value);
    }

    public function getMerchantPassword() {
        return $this->getParameter('merchantpassword');
    }

    public function setMerchantPassword($value) {
        return $this->setParameter('merchantpassword', $value);
    }
 
    public function getCurrency() {
        return $this->getParameter('currency');
    }

    public function setCurrency($value) {
        return $this->setParameter('currency', $value);
    }

    public function getSessionType() {
        return $this->getParameter('sessionType');
    }

    public function setSessionType($value) {
        return $this->setParameter('sessionType', $value);
    }

    public function getSessionToken() {
        return $this->getParameter('sessionToken');
    }

    public function setSessionToken($value) {
        return $this->setParameter('sessionToken', $value);
    }
 
    public function getSecretKey () {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value) {
        return $this->setParameter('secretKey', $value);
    }

    public function getQueryAction () {
        return $this->getParameter('queryAction');
    }

    public function setQueryAction($value) {
        return $this->setParameter('queryAction', $value);
    }

    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\AuthorizeRequest', $parameters);
    }

    public function completeAuthorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\CompletePaymentRequest', $parameters);
    }
    
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\CaptureRequest', $parameters);
    }

    public function session(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\SessionRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\CompletePaymentRequest', $parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\VoidRequest', $parameters);
    }

    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\RefundRequest', $parameters);
    }

    public function credit(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\CreditRequest', $parameters);
    }

    public function partialRefund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\PartialRefundRequest', $parameters);
    }

    public function query(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Paratika\Message\QueryRequest', $parameters);
    }

}
