<?php

namespace Omnipay\Paratika\Message;

use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;

/** Oturum açıldıysa ödeme sayfasına GET yönlendirmesi; açılmadıysa hata iletisi (getMessage) */
class PurchaseResponse extends ApiResponse implements RedirectResponseInterface
{
    public function __construct(RequestInterface $request, $data, protected ?string $redirectUrl)
    {
        parent::__construct($request, $data);
    }

    public function isSuccessful(): bool
    {
        return false;
    }

    public function isRedirect(): bool
    {
        return $this->redirectUrl !== null && is_array($this->data) && (string) ($this->data['responseCode'] ?? '') === '00';
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getRedirectMethod(): string
    {
        return 'GET';
    }

    public function getRedirectData(): array
    {
        return [];
    }

    public function getMessage(): ?string
    {
        if ($this->isRedirect()) {
            return null;
        }
        $m = trim(html_entity_decode((string) (($this->data['errorMsg'] ?? '') ?: ($this->data['responseMsg'] ?? '')), ENT_QUOTES, 'UTF-8'));

        return is_array($this->data) ? ($m !== '' ? $m : null) : 'Unreadable response';
    }

    public function getSessionToken(): ?string
    {
        return ($this->data['sessionToken'] ?? '') !== '' ? (string) $this->data['sessionToken'] : null;
    }
}
