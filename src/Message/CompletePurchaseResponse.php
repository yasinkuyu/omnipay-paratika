<?php

namespace Omnipay\Paratika\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;

/**
 * Dönüşün sonucu. isVerified(): imza (varsa) geçerli ve sonuç sunucudan sorguyla kesinleşti; false ise sonuç kaydedilmemeli.
 * Sorgudan gelen işlem satırı data['_tx'] içindedir.
 */
class CompletePurchaseResponse extends AbstractResponse implements NotificationInterface
{
    public function __construct(RequestInterface $request, $data, protected bool $verified, protected bool $approved = false)
    {
        parent::__construct($request, $data);
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function isSuccessful(): bool
    {
        return $this->verified && $this->approved;
    }

    public function getTransactionStatus(): string
    {
        if (!$this->verified) {
            return self::STATUS_PENDING;
        }

        return $this->approved ? self::STATUS_COMPLETED : self::STATUS_FAILED;
    }

    private function tx(): array
    {
        return is_array($this->data['_tx'] ?? null) ? $this->data['_tx'] : [];
    }

    /** Sipariş no (merchantPaymentId) */
    public function getTransactionId(): ?string
    {
        $v = (string) ($this->data['merchantPaymentId'] ?? '');

        return $v !== '' ? $v : null;
    }

    /** Paratika işlem numarası (PGTRANID) */
    public function getTransactionReference(): ?string
    {
        $v = (string) (($this->tx()['pgTranId'] ?? '') ?: ($this->data['pgTranId'] ?? ''));

        return $v !== '' ? $v : null;
    }

    public function getCode(): ?string
    {
        $v = (string) (($this->data['errorCode'] ?? '') ?: ($this->tx()['pgTranReturnCode'] ?? '') ?: ($this->data['responseCode'] ?? ''));

        return $v !== '' ? $v : null;
    }

    public function getMessage(): ?string
    {
        if (!$this->verified) {
            return 'Not verified';
        }
        if ($this->approved) {
            return null;
        }
        $m = (string) (($this->data['errorMsg'] ?? '') ?: ($this->data['pgTranErrorText'] ?? '') ?: ($this->tx()['pgTranErrorText'] ?? '') ?: ($this->data['responseMsg'] ?? ''));
        $m = trim(html_entity_decode($m, ENT_QUOTES, 'UTF-8'));

        return $m !== '' ? $m : null;
    }

    public function getAmount(): ?string
    {
        return isset($this->tx()['amount']) ? number_format((float) $this->tx()['amount'], 2, '.', '') : null;
    }

    public function getInstallment(): int
    {
        return max(1, (int) ($this->tx()['installmentCount'] ?? 1));
    }

    public function getMaskedCard(): ?string
    {
        $t = $this->tx();
        if (($t['panLast4'] ?? '') === '') {
            return null;
        }

        return (($t['bin'] ?? '') !== '' ? substr((string) $t['bin'], 0, 6) : '') . '******' . $t['panLast4'];
    }

    public function getAuthCode(): ?string
    {
        return ($this->tx()['pgTranApprCode'] ?? '') !== '' ? (string) $this->tx()['pgTranApprCode'] : null;
    }

    public function getHostReference(): ?string
    {
        return ($this->tx()['pgTranRefId'] ?? '') !== '' ? (string) $this->tx()['pgTranRefId'] : null;
    }
}
