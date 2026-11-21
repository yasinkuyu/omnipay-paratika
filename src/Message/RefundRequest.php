<?php

namespace Omnipay\Paratika\Message;

/** İade: transactionReference (PGTRANID) ya da transactionId (MERCHANTPAYMENTID), amount (verilmezse tamamı) */
class RefundRequest extends AbstractRequest
{
    public function getData(): array
    {
        if (!$this->getTransactionReference()) {
            $this->validate('transactionId');
        }
        $data = $this->credentials() + ['ACTION' => 'REFUND'];
        $this->getTransactionReference()
            ? $data['PGTRANID'] = (string) $this->getTransactionReference()
            : $data['MERCHANTPAYMENTID'] = (string) $this->getTransactionId();
        if ($this->getParameter('amount') !== null) {
            $data['AMOUNT'] = $this->getAmount();
            $data['CURRENCY'] = (string) ($this->getCurrency() ?: 'TRY');
        }

        return $data;
    }

    public function sendData($data): ApiResponse
    {
        return $this->response = new ApiResponse($this, $this->api($data));
    }
}
