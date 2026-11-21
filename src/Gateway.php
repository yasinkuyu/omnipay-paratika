<?php

namespace Omnipay\Paratika;

use Omnipay\Common\AbstractGateway;
use Omnipay\Paratika\Message\CompletePurchaseRequest;
use Omnipay\Paratika\Message\FetchTransactionRequest;
use Omnipay\Paratika\Message\PurchaseRequest;
use Omnipay\Paratika\Message\RefundRequest;
use Omnipay\Paratika\Message\VoidRequest;

/**
 * Paratika (Payten / Asseco) ortak ödeme sayfası (HPP). Aynı altyapı VakıfPayS ve ZiraatPay adıyla da sunulur
 * (platform: paratika | vakifpay | ziraatpay).
 *
 * purchase()          ödeme oturumu açar (SESSIONTOKEN); yanıt müşteriyi Paratika'nın ödeme sayfasına GET ile yönlendirir.
 *                     Kart bilgisi ve taksit seçimi o sayfadadır.
 * completePurchase()  RETURNURL dönüşü: sdSha512 imzası (secretKey verilmişse) + QUERYTRANSACTION ile sunucudan doğrulama.
 * refund() / void()   REFUND / VOID; fetchTransaction() QUERYTRANSACTION.
 *
 * (c) Yasin Kuyu — https://github.com/yasinkuyu/omnipay-paratika
 */
class Gateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'Paratika';
    }

    public function getDefaultParameters(): array
    {
        return [
            'platform' => 'paratika',
            'merchant' => '',
            'merchantUser' => '',
            'merchantPassword' => '',
            'secretKey' => '',
            'testMode' => false,
        ];
    }

    /** paratika | vakifpay | ziraatpay */
    public function getPlatform() { return $this->getParameter('platform'); }
    public function setPlatform($value): self { return $this->setParameter('platform', $value); }

    /** Listede olmayan kurulum için API adresi (…/api/v2, https) */
    public function getEndpoint() { return $this->getParameter('endpoint'); }
    public function setEndpoint($value): self { return $this->setParameter('endpoint', $value); }

    /** Üye iş yeri numarası */
    public function getMerchant() { return $this->getParameter('merchant'); }
    public function setMerchant($value): self { return $this->setParameter('merchant', $value); }

    /** API kullanıcısının e-posta adresi */
    public function getMerchantUser() { return $this->getParameter('merchantUser'); }
    public function setMerchantUser($value): self { return $this->setParameter('merchantUser', $value); }

    public function getMerchantPassword() { return $this->getParameter('merchantPassword'); }
    public function setMerchantPassword($value): self { return $this->setParameter('merchantPassword', $value); }

    /** Üye iş yeri gizli anahtarı (dönüş imzası sdSha512 için) */
    public function getSecretKey() { return $this->getParameter('secretKey'); }
    public function setSecretKey($value): self { return $this->setParameter('secretKey', $value); }

    public function purchase(array $options = []): PurchaseRequest
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    public function completePurchase(array $options = []): CompletePurchaseRequest
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    public function acceptNotification(array $options = []): CompletePurchaseRequest
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    public function refund(array $options = []): RefundRequest
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    public function void(array $options = []): VoidRequest
    {
        return $this->createRequest(VoidRequest::class, $options);
    }

    public function fetchTransaction(array $options = []): FetchTransactionRequest
    {
        return $this->createRequest(FetchTransactionRequest::class, $options);
    }
}
