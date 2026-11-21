<?php

namespace Omnipay\Paratika\Tests;

use Omnipay\Paratika\Gateway;
use Omnipay\Tests\GatewayTestCase;
use Symfony\Component\HttpFoundation\Request;

class GatewayTest extends GatewayTestCase
{
    /** @var Gateway */
    protected $gateway;

    private const CREDS = ['merchant' => '10000000', 'merchantUser' => 'api@example.com', 'merchantPassword' => 'secret', 'secretKey' => 'SKEY', 'testMode' => true];

    public function setUp(): void
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize(self::CREDS);
    }

    private function body(): array
    {
        parse_str((string) $this->getMockClient()->getLastRequest()->getBody(), $out);

        return $out;
    }

    public function testPurchaseOpensSessionAndRedirectsToPaymentPage(): void
    {
        $this->setMockHttpResponse('SessionSuccess.txt');
        $r = $this->gateway->purchase([
            'amount' => '149.90', 'currency' => 'TRY', 'transactionId' => 'SIP-1', 'returnUrl' => 'https://shop.test/r',
            'clientIp' => '1.2.3.4', 'allowedInstallments' => [1, 3, 6, 6],
            'card' => ['email' => 'a@b.test', 'billingFirstName' => 'Ada', 'billingLastName' => 'Kaya', 'billingPhone' => '+90 555 111 22 33'],
            'items' => [['name' => 'Kazak', 'quantity' => 2, 'price' => '50.00'], ['name' => 'Kargo', 'quantity' => 1, 'price' => '49.90']],
        ])->send();

        $this->assertTrue($r->isRedirect());
        $this->assertSame('GET', $r->getRedirectMethod());
        $this->assertSame('https://entegrasyon.paratika.com.tr/payment/HCZVQH5FIR5QBHBQCT6AMUJLVHEHXCMSQ2HA5I6WCGQKQNQX', $r->getRedirectUrl());

        $b = $this->body();
        $this->assertSame('SESSIONTOKEN', $b['ACTION']);
        $this->assertSame('PAYMENTSESSION', $b['SESSIONTYPE']);
        $this->assertSame('SIP-1', $b['MERCHANTPAYMENTID']);
        $this->assertSame('149.90', $b['AMOUNT']);
        $this->assertSame('3;6', $b['ALLOWEDINSTALLMENTS']);
        $this->assertSame('+905551112233', $b['CUSTOMERPHONE']);
        $items = json_decode($b['ORDERITEMS'], true);
        $this->assertSame(['Kazak x2', '100.00'], [$items[0]['name'], $items[0]['amount']]);
        $this->assertSame('https://entegrasyon.paratika.com.tr/paratika/api/v2', (string) $this->getMockClient()->getLastRequest()->getUri());
    }

    public function testItemsNotMatchingTotalFallBackToSingleLine(): void
    {
        $this->setMockHttpResponse('SessionSuccess.txt');
        $this->gateway->purchase(['amount' => '10.00', 'transactionId' => 'SIP-2', 'returnUrl' => 'https://shop.test/r',
            'items' => [['name' => 'A', 'quantity' => 1, 'price' => '3.00']]])->send();
        $items = json_decode($this->body()['ORDERITEMS'], true);
        $this->assertCount(1, $items);
        $this->assertSame('10.00', $items[0]['amount']);
    }

    public function testPurchaseFailure(): void
    {
        $this->setMockHttpResponse('SessionFailure.txt');
        $r = $this->gateway->purchase(['amount' => '1.00', 'transactionId' => 'X', 'returnUrl' => 'https://shop.test/r'])->send();
        $this->assertFalse($r->isRedirect());
        $this->assertSame('Geçersiz kullanıcı bilgileri', $r->getMessage());
    }

    public function testPlatforms(): void
    {
        $this->setMockHttpResponse('SessionSuccess.txt');
        $this->gateway->setTestMode(false);
        $this->gateway->setPlatform('vakifpay');
        $r = $this->gateway->purchase(['amount' => '1.00', 'transactionId' => 'X', 'returnUrl' => 'https://shop.test/r'])->send();
        $this->assertSame('https://pos.vakifpays.com.tr/vakifpays/api/v2', (string) $this->getMockClient()->getLastRequest()->getUri());
        $this->assertStringStartsWith('https://pos.vakifpays.com.tr/payment/', $r->getRedirectUrl());
    }

    private function returnFields(array $o = []): array
    {
        $f = array_merge(['merchantPaymentId' => 'SIP-1', 'customerId' => 'a@b.test', 'sessionToken' => 'TOK', 'responseCode' => '00',
            'responseMsg' => 'Approved', 'random' => 'R1', 'pgTranId' => '18285OQZD14766'], $o);
        $f['sdSha512'] = $o['sdSha512'] ?? hash('sha512', "{$f['merchantPaymentId']}|{$f['customerId']}|{$f['sessionToken']}|{$f['responseCode']}|{$f['random']}|SKEY");

        return $f;
    }

    private function returning(array $fields): void
    {
        $this->gateway = new Gateway($this->getHttpClient(), Request::create('/r', 'POST', $fields));
        $this->gateway->initialize(self::CREDS);
    }

    public function testCompletePurchaseApproved(): void
    {
        $this->setMockHttpResponse('QueryApproved.txt');
        $this->returning($this->returnFields());
        $r = $this->gateway->completePurchase(['transactionId' => 'SIP-1', 'amount' => '149.90'])->send();

        $this->assertTrue($r->isVerified());
        $this->assertTrue($r->isSuccessful());
        $this->assertSame('18285OQZD14766', $r->getTransactionReference());
        $this->assertSame(3, $r->getInstallment());
        $this->assertSame('545616******5454', $r->getMaskedCard());
        $this->assertSame('149.90', $r->getAmount());
        $b = $this->body();
        $this->assertSame(['QUERYTRANSACTION', 'SIP-1'], [$b['ACTION'], $b['MERCHANTPAYMENTID']]);
    }

    public function testTamperedSignatureIsRejectedWithoutQuery(): void
    {
        $f = $this->returnFields();
        $f['merchantPaymentId'] = 'SIP-1';
        $f['responseCode'] = '00';
        $f['sdSha512'] = str_repeat('a', 128);
        $this->returning($f);
        $r = $this->gateway->completePurchase(['transactionId' => 'SIP-1'])->send();

        $this->assertFalse($r->isVerified());
        $this->assertFalse($this->getMockClient()->getLastRequest());
    }

    public function testOtherOrdersReturnIsRejected(): void
    {
        $this->returning($this->returnFields(['merchantPaymentId' => 'SIP-9']));
        $this->assertFalse($this->gateway->completePurchase(['transactionId' => 'SIP-1'])->send()->isVerified());
    }

    public function testAmountMismatchIsNotAccepted(): void
    {
        $this->setMockHttpResponse('QueryApproved.txt');
        $this->returning($this->returnFields());
        $r = $this->gateway->completePurchase(['transactionId' => 'SIP-1', 'amount' => '999.00'])->send();
        $this->assertFalse($r->isVerified());
        $this->assertFalse($r->isSuccessful());
    }

    public function testDeclined(): void
    {
        $this->setMockHttpResponse('QueryDeclined.txt');
        $this->returning($this->returnFields(['responseCode' => '99', 'errorMsg' => 'Yetersiz bakiye']));
        $r = $this->gateway->completePurchase(['transactionId' => 'SIP-1'])->send();
        $this->assertTrue($r->isVerified());
        $this->assertFalse($r->isSuccessful());
        $this->assertSame('Yetersiz bakiye', $r->getMessage());
        $this->assertSame('failed', $r->getTransactionStatus());
    }

    public function testApprovedClaimWithoutApprovedSaleStaysPending(): void
    {
        // anahtar yok: imza denetlenemez; sorgu onaylı satış göstermiyor → sonuç kaydedilmez
        $this->setMockHttpResponse('QueryEmpty.txt');
        $this->gateway = new Gateway($this->getHttpClient(), Request::create('/r', 'POST', ['merchantPaymentId' => 'SIP-1', 'responseCode' => '00']));
        $this->gateway->initialize(['secretKey' => ''] + self::CREDS);
        $r = $this->gateway->completePurchase(['transactionId' => 'SIP-1'])->send();
        $this->assertFalse($r->isVerified());
        $this->assertSame('pending', $r->getTransactionStatus());
    }

    public function testUnreadableQueryStaysPending(): void
    {
        $this->setMockHttpResponse('Unreadable.txt');
        $this->returning($this->returnFields());
        $this->assertFalse($this->gateway->completePurchase(['transactionId' => 'SIP-1'])->send()->isVerified());
    }

    public function testPeekWithoutTransactionIdMakesNoRequest(): void
    {
        $this->returning($this->returnFields());
        $r = $this->gateway->completePurchase()->send();
        $this->assertFalse($r->isVerified());
        $this->assertSame('SIP-1', $r->getTransactionId());
        $this->assertFalse($this->getMockClient()->getLastRequest());
    }

    public function testRefundAndVoid(): void
    {
        $this->setMockHttpResponse('RefundSuccess.txt');
        $r = $this->gateway->refund(['transactionReference' => '14339MjuG01015018', 'amount' => '50.00', 'currency' => 'TRY'])->send();
        $this->assertTrue($r->isSuccessful());
        $this->assertSame(['REFUND', '14339MjuG01015018', '50.00'], [$this->body()['ACTION'], $this->body()['PGTRANID'], $this->body()['AMOUNT']]);

        $this->setMockHttpResponse('SessionFailure.txt');
        $v = $this->gateway->void(['transactionId' => 'SIP-1'])->send();
        $this->assertFalse($v->isSuccessful());
        $this->assertSame(['VOID', 'SIP-1'], [$this->body()['ACTION'], $this->body()['MERCHANTPAYMENTID']]);

        $this->setMockHttpResponse('Unreadable.txt');
        $this->assertFalse($this->gateway->refund(['transactionId' => 'SIP-1'])->send()->isReadable());
    }
}
