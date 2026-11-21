<?php

namespace Omnipay\Paratika\Message;

/**
 * Ödeme sayfasından RETURNURL'e dönüş. Doğrulama iki adımlı:
 *  1. secretKey verilmişse ve dönüşte sdSha512 varsa imza denetlenir:
 *     SHA512 hex (merchantPaymentId|customerId|sessionToken|responseCode|random|secretKey); tutmazsa dönüş reddedilir.
 *  2. transactionId (kendi kaydınızdaki sipariş no; dönüş gövdesinden alınmaz) ile QUERYTRANSACTION: onaylı (AP) satış
 *     bulunursa ödeme başarılıdır; amount verilmişse tutar da karşılaştırılır.
 * transactionId verilmezse ağ isteği yapılmaz ve sonuç doğrulanmamış sayılır (yalnız dönüşteki sipariş no okunur).
 */
class CompletePurchaseRequest extends AbstractRequest
{
    public function getFields() { return $this->getParameter('fields'); }
    public function setFields($value) { return $this->setParameter('fields', $value); }

    public function getData(): array
    {
        if (is_array($this->getFields())) {
            return $this->getFields();
        }

        return $this->httpRequest->request->all() ?: $this->httpRequest->query->all();
    }

    public function sendData($data): CompletePurchaseResponse
    {
        $hashOk = $this->checkHash($data);
        $expected = $this->getTransactionId();
        if ($hashOk === false || $expected === null || $expected === '') {
            return $this->response = new CompletePurchaseResponse($this, $data, false);
        }
        if (isset($data['merchantPaymentId']) && (string) $data['merchantPaymentId'] !== (string) $expected) {
            return $this->response = new CompletePurchaseResponse($this, $data, false);
        }

        try {
            $q = $this->api($this->credentials() + ['ACTION' => 'QUERYTRANSACTION', 'MERCHANTPAYMENTID' => (string) $expected]);
        } catch (\Throwable $e) {
            $q = null;   // sorgu yapılamadı: sonuç belirsiz, kaydedilmez
        }
        if (!is_array($q) || (string) ($q['responseCode'] ?? '') !== '00') {
            return $this->response = new CompletePurchaseResponse($this, $data + ['_query' => $q], false);
        }

        $sales = array_values(array_filter((array) ($q['transactionList'] ?? []), fn ($t) => is_array($t)
            && in_array(strtoupper((string) ($t['transactionType'] ?? '')), ['SALE', 'PREAUTH'], true)));
        $approved = null;
        foreach ($sales as $t) {
            if (strtoupper((string) ($t['transactionStatus'] ?? '')) === 'AP') {
                $approved = $t;
                break;
            }
        }
        if ($approved) {
            $amountOk = $this->getParameter('amount') === null
                || abs((float) ($approved['amount'] ?? 0) - (float) $this->getAmount()) < 0.01;

            return $this->response = new CompletePurchaseResponse($this, ['_tx' => $approved] + $data, $amountOk, $amountOk);
        }
        $declined = null;
        foreach ($sales as $t) {
            if (in_array(strtoupper((string) ($t['transactionStatus'] ?? '')), ['FA', 'CA'], true)) {
                $declined = $t;
            }
        }
        if ($declined || ($hashOk === true && (string) ($data['responseCode'] ?? '') !== '00')) {
            return $this->response = new CompletePurchaseResponse($this, ['_tx' => $declined] + $data, true, false);
        }

        // işlem sürüyor ya da bulunamadı: belirsiz
        return $this->response = new CompletePurchaseResponse($this, $data, false);
    }

    /** null = denetlenmedi (anahtar ya da imza yok), true / false = imza sonucu */
    private function checkHash(array $data): ?bool
    {
        $key = (string) $this->getSecretKey();
        $given = (string) ($data['sdSha512'] ?? '');
        if ($key === '' || $given === '') {
            return null;
        }
        $calc = hash('sha512', implode('|', [
            (string) ($data['merchantPaymentId'] ?? ''), (string) ($data['customerId'] ?? ''), (string) ($data['sessionToken'] ?? ''),
            (string) ($data['responseCode'] ?? ''), (string) ($data['random'] ?? ''), $key,
        ]));

        return hash_equals($calc, strtolower($given));
    }
}
