# Omnipay: Paratika

**Paratika (Payten / Asseco) ortak ödeme sayfası sürücüsü — [Omnipay](https://github.com/thephpleague/omnipay) v3**

[![Latest Stable Version](https://poser.pugx.org/yasinkuyu/omnipay-paratika/v/stable)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)
[![Total Downloads](https://poser.pugx.org/yasinkuyu/omnipay-paratika/downloads)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)
[![License](https://poser.pugx.org/yasinkuyu/omnipay-paratika/license)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)

Aynı altyapı üç adla sunulur; `platform` ile seçilir:

| platform | Ürün |
|---|---|
| `paratika` | Paratika |
| `vakifpay` | VakıfPayS (Vakıfbank) |
| `ziraatpay` | ZiraatPay (Ziraat Bankası) |

- **Ortak ödeme sayfası (HPP):** kart bilgisi ve taksit seçimi Paratika'nın sayfasında, sizin sunucunuzdan geçmez
- Dönüş doğrulaması: `sdSha512` imzası + `QUERYTRANSACTION` ile sunucudan sorgu (onaylı satış görülmeden ödeme başarılı sayılmaz)
- İade (tam / kısmi), iptal, işlem sorgusu

## Kurulum

```bash
composer require yasinkuyu/omnipay-paratika:^3.0 php-http/guzzle7-adapter
```

PHP 8.1+ ve `omnipay/common` ^3.3 gerekir. 2.x sürümü `master` dalında duruyor.

## Kullanım

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Paratika');
$gateway->initialize([
    'platform'         => 'paratika',          // paratika | vakifpay | ziraatpay
    'merchant'         => '10000000',          // üye iş yeri no
    'merchantUser'     => 'api@magaza.com',    // API kullanıcısı
    'merchantPassword' => '********',
    'secretKey'        => '********',          // dönüş imzası (sdSha512) için, önerilir
    'testMode'         => true,
]);
```

### 1. Ödeme oturumu ve yönlendirme

```php
$response = $gateway->purchase([
    'amount'              => '149.90',
    'currency'            => 'TRY',
    'transactionId'       => 'SIP-10045',                       // MERCHANTPAYMENTID
    'returnUrl'           => 'https://magaza.com/odeme/donus',
    'clientIp'            => $_SERVER['REMOTE_ADDR'],
    'allowedInstallments' => [2, 3, 6],                         // ödeme sayfasında sunulacak taksitler
    'card'  => ['email' => 'ali@ornek.com', 'billingFirstName' => 'Ali', 'billingLastName' => 'Kaya', 'billingPhone' => '5551112233'],
    'items' => [['name' => 'Kazak', 'quantity' => 2, 'price' => '50.00'], ['name' => 'Kargo', 'quantity' => 1, 'price' => '49.90']],
])->send();

if ($response->isRedirect()) {
    $response->redirect();             // https://…/payment/{oturum}
} else {
    echo $response->getMessage();      // ör. "Geçersiz kullanıcı bilgileri"
}
```

Sepet satırlarının toplamı tutarı tutmazsa tek satır ("Siparis …") gönderilir.

### 2. Dönüş

```php
$result = $gateway->completePurchase([
    'transactionId' => 'SIP-10045', // KENDİ kaydınızdaki sipariş no (dönüş gövdesinden alınmaz)
    'amount'        => '149.90',    // isteğe bağlı: sorgudaki tutarla karşılaştırılır
])->send();

if (!$result->isVerified()) {
    // imza tutmadı ya da sonuç sorguyla kesinleşmedi: kaydetmeyin, sonra fetchTransaction ile yeniden bakın
} elseif ($result->isSuccessful()) {
    $result->getTransactionReference(); // PGTRANID (iade / iptal için saklayın)
    $result->getInstallment();
    $result->getMaskedCard();           // 545616******5454
} else {
    $result->getMessage();
}
```

### İade, iptal, sorgu

```php
$gateway->void(['transactionReference' => $pgTranId])->send();                       // gün sonundan önce
$gateway->refund(['transactionReference' => $pgTranId, 'amount' => '50.00'])->send(); // tam / kısmi
$gateway->fetchTransaction(['transactionId' => 'SIP-10045'])->send()->getTransactions();
```

`isReadable()` false ise yanıt okunamadı: işlem gerçekleşmiş olabilir, otomatik tekrar denemeyin.

## Testler

```bash
composer install && vendor/bin/phpunit
```

## Lisans

MIT — Yasin Kuyu
