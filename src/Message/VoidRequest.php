<?php

namespace Omnipay\Paratika\Message;

/** İptal (gün sonundan önce): transactionReference (PGTRANID) ya da transactionId (MERCHANTPAYMENTID) */
class VoidRequest extends AbstractRequest
{
    public function getData(): array
    {
        if (!$this->getTransactionReference()) {
            $this->validate('transactionId');
        }
        $data = $this->credentials() + ['ACTION' => 'VOID'];
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
