<?php

namespace Omnipay\Paratika\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Ortak parametreler, platform adresleri ve API çağrısı (form POST → JSON).
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    /** platform => [test API, canlı API] */
    public const PLATFORMS = [
        'paratika' => ['https://entegrasyon.paratika.com.tr/paratika/api/v2', 'https://vpos.paratika.com.tr/paratika/api/v2'],
        'vakifpay' => ['https://testpos.vakifpays.com.tr/vakifpays/api/v2', 'https://pos.vakifpays.com.tr/vakifpays/api/v2'],
        'ziraatpay' => ['https://test.ziraatpay.com.tr/ziraatpay/api/v2', 'https://vpos.ziraatpay.com.tr/ziraatpay/api/v2'],
    ];

    public function getPlatform() { return $this->getParameter('platform'); }
    public function setPlatform($value) { return $this->setParameter('platform', $value); }

    public function getEndpoint() { return $this->getParameter('endpoint'); }
    public function setEndpoint($value) { return $this->setParameter('endpoint', $value); }

    public function getMerchant() { return $this->getParameter('merchant'); }
    public function setMerchant($value) { return $this->setParameter('merchant', $value); }

    public function getMerchantUser() { return $this->getParameter('merchantUser'); }
    public function setMerchantUser($value) { return $this->setParameter('merchantUser', $value); }

    public function getMerchantPassword() { return $this->getParameter('merchantPassword'); }
    public function setMerchantPassword($value) { return $this->setParameter('merchantPassword', $value); }

    public function getSecretKey() { return $this->getParameter('secretKey'); }
    public function setSecretKey($value) { return $this->setParameter('secretKey', $value); }

    /** API adresi (…/api/v2) */
    public function getApiUrl(): string
    {
        $custom = trim((string) $this->getEndpoint());
        if ($custom !== '') {
            if (!str_starts_with(strtolower($custom), 'https://') || !filter_var($custom, FILTER_VALIDATE_URL)) {
                throw new InvalidRequestException('Paratika endpoint must be an https URL.');
            }

            return rtrim($custom, '/');
        }
        $p = strtolower((string) ($this->getPlatform() ?: 'paratika'));
        if (!isset(self::PLATFORMS[$p])) {
            throw new InvalidRequestException('Unknown Paratika platform: ' . $p);
        }

        return self::PLATFORMS[$p][$this->getTestMode() ? 0 : 1];
    }

    /** Ortak ödeme sayfası: {alan adı}/payment/{oturum} */
    public function getPaymentPageUrl(string $sessionToken): string
    {
        $u = parse_url($this->getApiUrl());

        return $u['scheme'] . '://' . $u['host'] . (isset($u['port']) ? ':' . $u['port'] : '') . '/payment/' . rawurlencode($sessionToken);
    }

    protected function credentials(): array
    {
        $this->validate('merchant', 'merchantUser', 'merchantPassword');

        return [
            'MERCHANT' => (string) $this->getMerchant(),
            'MERCHANTUSER' => (string) $this->getMerchantUser(),
            'MERCHANTPASSWORD' => (string) $this->getMerchantPassword(),
        ];
    }

    /**
     * API isteği. Okunamayan yanıtta null (işlem gerçekleşmiş olabilir); bağlantı hatasında Omnipay'in NetworkException'ı fırlar.
     */
    protected function api(array $fields): ?array
    {
        $res = $this->httpClient->request('POST', $this->getApiUrl(), [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            'Accept' => 'application/json',
        ], http_build_query($fields, '', '&', PHP_QUERY_RFC3986));
        $json = json_decode((string) $res->getBody(), true);

        return is_array($json) ? $json : null;
    }
}
