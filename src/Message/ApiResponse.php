<?php

namespace Omnipay\Paratika\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * API yanıtı (REFUND, VOID, QUERYTRANSACTION). isSuccessful(): responseCode = 00.
 * isReadable() false ise yanıt okunamadı: işlem gerçekleşmiş olabilir, otomatik tekrar denenmemeli.
 */
class ApiResponse extends AbstractResponse
{
    public function isReadable(): bool
    {
        return is_array($this->data);
    }

    public function isSuccessful(): bool
    {
        return is_array($this->data) && (string) ($this->data['responseCode'] ?? '') === '00';
    }

    public function getCode(): ?string
    {
        $c = (string) (($this->data['errorCode'] ?? '') ?: ($this->data['responseCode'] ?? ''));

        return $c !== '' ? $c : null;
    }

    public function getMessage(): ?string
    {
        if (!is_array($this->data)) {
            return 'Unreadable response';
        }
        if ($this->isSuccessful()) {
            return null;
        }
        $m = trim(html_entity_decode((string) (($this->data['errorMsg'] ?? '') ?: ($this->data['responseMsg'] ?? '')), ENT_QUOTES, 'UTF-8'));

        return $m !== '' ? $m : null;
    }

    /** Paratika işlem numarası (PGTRANID) */
    public function getTransactionReference(): ?string
    {
        return ($this->data['pgTranId'] ?? '') !== '' ? (string) $this->data['pgTranId'] : null;
    }

    public function getTransactionId(): ?string
    {
        return ($this->data['merchantPaymentId'] ?? '') !== '' ? (string) $this->data['merchantPaymentId'] : null;
    }

    /** QUERYTRANSACTION: işlem listesi */
    public function getTransactions(): array
    {
        return is_array($this->data['transactionList'] ?? null) ? $this->data['transactionList'] : [];
    }
}
