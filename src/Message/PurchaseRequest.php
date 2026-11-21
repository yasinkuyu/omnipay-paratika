<?php

namespace Omnipay\Paratika\Message;

/**
 * Ödeme oturumu (SESSIONTOKEN, PAYMENTSESSION). Parametreler: amount, currency, transactionId (MERCHANTPAYMENTID),
 * returnUrl, card (ad, e-posta, telefon, adres), clientIp, items (yoksa tek satır), allowedInstallments ([2,3,6] ya da
 * "2;3;6": ödeme sayfasında sunulacak taksitler), language, sessionExpiry (ör. "1h").
 *
 * Sepet satırları tutar tutsun diye her satır adet 1 ve satır toplamıyla gönderilir (ERR10022: satır toplamı = tutar).
 */
class PurchaseRequest extends AbstractRequest
{
    public function getAllowedInstallments() { return $this->getParameter('allowedInstallments'); }
    public function setAllowedInstallments($value) { return $this->setParameter('allowedInstallments', $value); }

    public function getLanguage() { return $this->getParameter('language'); }
    public function setLanguage($value) { return $this->setParameter('language', $value); }

    public function getSessionExpiry() { return $this->getParameter('sessionExpiry'); }
    public function setSessionExpiry($value) { return $this->setParameter('sessionExpiry', $value); }

    public function getData(): array
    {
        $this->validate('amount', 'transactionId', 'returnUrl');
        $card = $this->getCard();
        $name = $card ? trim((string) $card->getBillingName()) : '';
        $email = $card ? (string) $card->getEmail() : '';

        $data = $this->credentials() + [
            'ACTION' => 'SESSIONTOKEN',
            'SESSIONTYPE' => 'PAYMENTSESSION',
            'MERCHANTPAYMENTID' => (string) $this->getTransactionId(),
            'AMOUNT' => $this->getAmount(),
            'CURRENCY' => (string) ($this->getCurrency() ?: 'TRY'),
            'RETURNURL' => (string) $this->getReturnUrl(),
            'CUSTOMER' => mb_substr($email !== '' ? $email : 'C' . $this->getTransactionId(), 0, 128),
            'CUSTOMERNAME' => mb_substr($name !== '' ? $name : 'Musteri', 0, 128),
            'CUSTOMEREMAIL' => mb_substr($email, 0, 128),
            'CUSTOMERPHONE' => mb_substr(preg_replace('/[^0-9+]/', '', $card ? (string) $card->getBillingPhone() : ''), 0, 64),
            'CUSTOMERIP' => (string) ($this->getClientIp() ?: ''),
            'LANGUAGE' => (string) ($this->getLanguage() ?: 'tr'),
            'ORDERITEMS' => json_encode($this->orderItems(), JSON_UNESCAPED_UNICODE),
        ];
        if ($card) {
            $data += array_filter([
                'BILLTOADDRESSLINE' => mb_substr(trim($card->getBillingAddress1() . ' ' . $card->getBillingAddress2()), 0, 255),
                'BILLTOCITY' => (string) $card->getBillingCity(),
                'BILLTOCOUNTRY' => (string) $card->getBillingCountry(),
                'BILLTOPOSTALCODE' => (string) $card->getBillingPostcode(),
                'SHIPTOADDRESSLINE' => mb_substr(trim($card->getShippingAddress1() . ' ' . $card->getShippingAddress2()), 0, 255),
                'SHIPTOCITY' => (string) $card->getShippingCity(),
                'SHIPTOCOUNTRY' => (string) $card->getShippingCountry(),
            ], fn ($v) => $v !== '');
        }
        $inst = $this->getAllowedInstallments();
        if (is_string($inst)) {
            $inst = preg_split('/[^0-9]+/', $inst);
        }
        $inst = array_values(array_unique(array_filter(array_map('intval', (array) $inst), fn ($n) => $n >= 2 && $n <= 12)));
        if ($inst) {
            sort($inst);
            $data['ALLOWEDINSTALLMENTS'] = implode(';', $inst);
        }
        if ($this->getSessionExpiry()) {
            $data['SESSIONEXPIRY'] = (string) $this->getSessionExpiry();
        }

        return array_filter($data, fn ($v) => $v !== '' && $v !== null);
    }

    private function orderItems(): array
    {
        $out = [];
        $items = $this->getItems();
        if ($items && count($items)) {
            $i = 0;
            foreach ($items as $item) {
                $qty = max(1, (int) $item->getQuantity());
                $line = round((float) $item->getPrice() * $qty, 2);
                $name = mb_substr(trim((string) $item->getName()) ?: 'Urun', 0, 120) . ($qty > 1 ? ' x' . $qty : '');
                $out[] = ['productCode' => 'L' . (++$i), 'name' => $name, 'description' => mb_substr((string) ($item->getDescription() ?: $name), 0, 255),
                    'quantity' => 1, 'amount' => number_format($line, 2, '.', '')];
            }
            $sum = round(array_sum(array_column($out, 'amount')), 2);
            if (abs($sum - (float) $this->getAmount()) < 0.01) {
                return $out;
            }
        }
        $desc = (string) ($this->getDescription() ?: 'Siparis ' . $this->getTransactionId());

        return [['productCode' => 'ORDER', 'name' => mb_substr($desc, 0, 120), 'description' => mb_substr($desc, 0, 255), 'quantity' => 1, 'amount' => $this->getAmount()]];
    }

    public function sendData($data): PurchaseResponse
    {
        $json = $this->api($data);

        return $this->response = new PurchaseResponse($this, $json, $json && ($json['sessionToken'] ?? '') !== ''
            ? $this->getPaymentPageUrl((string) $json['sessionToken']) : null);
    }
}
