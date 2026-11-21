<?php

namespace Omnipay\Paratika\Message;

/** İşlem sorgusu (QUERYTRANSACTION): transactionReference (PGTRANID) ya da transactionId (MERCHANTPAYMENTID) */
class FetchTransactionRequest extends AbstractRequest
{
    public function getData(): array
    {
        if (!$this->getTransactionReference()) {
            $this->validate('transactionId');
        }
        $data = $this->credentials() + ['ACTION' => 'QUERYTRANSACTION'];
        $this->getTransactionReference()
            ? $data['PGTRANID'] = (string) $this->getTransactionReference()
            : $data['MERCHANTPAYMENTID'] = (string) $this->getTransactionId();

        return $data;
    }

    public function sendData($data): ApiResponse
    {
        return $this->response = new ApiResponse($this, $this->api($data));
    }
}
