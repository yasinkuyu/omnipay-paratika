# Omnipay: Paratika

**Paratika, VakıfPayS ve ZiraatPay ortak ödeme sayfası için [Omnipay](https://github.com/thephpleague/omnipay) sürücüsü**

[![Latest Stable Version](https://poser.pugx.org/yasinkuyu/omnipay-paratika/v/stable)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)
[![Total Downloads](https://poser.pugx.org/yasinkuyu/omnipay-paratika/downloads)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)
[![License](https://poser.pugx.org/yasinkuyu/omnipay-paratika/license)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)

**Türkçe** | [English](README_EN.md)

## Bu paket ne yapar?

Mağazanızda kartla ödeme almanızı sağlar. Sunucunuz Paratika'da bir **ödeme oturumu** açar, müşteri Paratika'nın
**güvenli ödeme sayfasına** gider, kartını ve taksitini orada seçer, sitenize geri döner. Kart bilgisi sizin
sunucunuzdan hiç geçmez.

```
Sunucunuz ──(oturum aç)──▶ Paratika API ──▶ oturum anahtarı
Müşteri ──▶ Paratika ödeme sayfası (kart + taksit + 3D Secure) ──▶ Siteniz (dönüş adresi)
Sunucunuz ──(işlem sorgusu)──▶ Paratika API ──▶ ödeme kesinleşir
```

**Aynı altyapı, üç ürün** (`platform` ayarı):

| `platform` | Ürün | Kimin? |
|---|---|---|
| `paratika` | Paratika | Payten; birçok bankanın taksit kampanyası tek hesapta |
| `vakifpay` | VakıfPayS | Vakıfbank |
| `ziraatpay` | ZiraatPay | Ziraat Bankası |

**Özellikler**

| | |
|---|---|
| Ödeme | Ortak ödeme sayfası (HPP), 3D Secure sağlayıcıda |
| Taksit | Müşteri sağlayıcının sayfasında seçer; hangi taksitlerin sunulacağını siz belirlersiniz |
| İade / iptal | `refund()` (tam ya da kısmi), `void()` (gün sonundan önce) |
| Sorgu | `fetchTransaction()` |
| Güvenlik | Dönüşte `sdSha512` imzası + sunucudan işlem sorgusu + tutar denetimi |

## Kurulum

```bash
composer require yasinkuyu/omnipay-paratika php-http/guzzle7-adapter
```

PHP 8.1 ya da üstü gerekir.

## Sağlayıcıdan almanız gereken bilgiler

| Ayar | Nedir? | Nereden bulunur? |
|---|---|---|
| `merchant` | Üye iş yeri numarası | Hesap açılışında gönderilen bilgiler / yönetim paneli |
| `merchantUser` | API kullanıcısının e-posta adresi | Yönetim panelinde **API yetkili** bir kullanıcı açın |
| `merchantPassword` | API kullanıcısının şifresi | Aynı kullanıcı |
| `secretKey` | Üye iş yeri gizli anahtarı | Yönetim paneli → üye iş yeri ayarları. İsteğe bağlı ama **önerilir** |

Test ortamı için sağlayıcı ayrı bir üye iş yeri hesabı verir; herkese açık ortak bir test hesabı yoktur.

## Hızlı başlangıç

### 1. Gateway'i hazırlayın

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Paratika');
$gateway->initialize([
    'platform'         => 'paratika',        // paratika | vakifpay | ziraatpay
    'merchant'         => '10000000',
    'merchantUser'     => 'api@magaza.com',
    'merchantPassword' => '********',
    'secretKey'        => '********',
    'testMode'         => true,              // canlıya geçerken false
]);
```

### 2. Ödemeyi başlatın (odeme.php)

```php
$response = $gateway->purchase([
    'amount'              => '149.90',
    'currency'            => 'TRY',
    'transactionId'       => 'SIP-10045',                          // sizin sipariş numaranız, her denemede benzersiz
    'returnUrl'           => 'https://magaza.com/odeme-donus.php',
    'clientIp'            => $_SERVER['REMOTE_ADDR'],
    'allowedInstallments' => [2, 3, 6],                            // sunulacak taksitler (2-12)
    'card'  => [                                                   // kart numarası YOK; yalnız müşteri bilgisi
        'email'            => 'ali@ornek.com',
        'billingFirstName' => 'Ali',
        'billingLastName'  => 'Kaya',
        'billingPhone'     => '5551112233',
    ],
    'items' => [                                                   // isteğe bağlı; toplamı tutara eşit olmalı
        ['name' => 'Kazak', 'quantity' => 2, 'price' => '50.00'],
        ['name' => 'Kargo', 'quantity' => 1, 'price' => '49.90'],
    ],
])->send();

if ($response->isRedirect()) {
    $response->redirect();            // müşteriyi https://…/payment/{oturum} adresine götürür
} else {
    echo $response->getMessage();     // ör. "Geçersiz kullanıcı bilgileri"
}
```

Sepet satırlarının toplamı tutarı tutmazsa paket tek satır ("Siparis SIP-10045") gönderir; sağlayıcı toplamın
tutmasını şart koşar.

### 3. Dönüşü doğrulayın (odeme-donus.php)

```php
$result = $gateway->completePurchase([
    'transactionId' => 'SIP-10045', // KENDİ kaydınızdaki sipariş no (dönüşten okunmaz)
    'amount'        => '149.90',    // KENDİ kaydınızdaki tutar
])->send();

if (!$result->isVerified()) {
    // Sonuç kesinleşmedi: imza tutmadı, sorgu yapılamadı ya da işlem hala sürüyor.
    // Siparişi ödenmiş saymayın; biraz sonra fetchTransaction() ile yeniden bakın.
} elseif ($result->isSuccessful()) {
    // Ödeme alındı.
    $pgTranId = $result->getTransactionReference(); // iade / iptal için saklayın
    $result->getInstallment();
    $result->getMaskedCard();                       // 545616******5454
} else {
    echo $result->getMessage();                     // ret nedeni
}
```

> **Doğrulama nasıl çalışır?** Önce dönüşteki `sdSha512` imzası `secretKey` ile denetlenir. Sonra paket, sizin
> verdiğiniz sipariş numarasıyla Paratika'ya sunucudan sorar. Onaylı bir satış ve doğru tutar görülmeden ödeme
> başarılı sayılmaz. Bu yüzden dönüş sayfası bir API isteği yapar.

## İade, iptal, sorgu

```php
// Gün sonundan önce: iptal
$gateway->void(['transactionReference' => $pgTranId])->send();

// Tam ya da kısmi iade
$gateway->refund(['transactionReference' => $pgTranId, 'amount' => '50.00', 'currency' => 'TRY'])->send();

// İşlemler
$gateway->fetchTransaction(['transactionId' => 'SIP-10045'])->send()->getTransactions();
```

`transactionReference` (Paratika işlem no) yerine `transactionId` (sizin sipariş no) da verebilirsiniz.

Her yanıtta `isSuccessful()` ve `getMessage()` vardır. **`isReadable()` false dönerse** yanıt okunamamıştır: işlem
gerçekleşmiş olabilir. Otomatik olarak yeniden denemeyin; yönetim panelinden kontrol edin.

## Diğer ayarlar

| Ayar | Varsayılan | Açıklama |
|---|---|---|
| `language` | `tr` | Ödeme sayfasının dili |
| `sessionExpiry` | sağlayıcının varsayılanı (7 gün) | Oturum süresi, ör. `1h` |
| `endpoint` | | Listede olmayan kurulum için API adresi (`https://…/api/v2`) |

## Sık karşılaşılan durumlar

| Hata | Neden |
|---|---|
| `ERR10020` Geçersiz kullanıcı bilgileri | `merchantUser` / `merchantPassword` yanlış ya da test ve canlı ortam karıştı |
| `ERR10010` Zorunlu parametre yok | Hatanın `violatorParam` alanına bakın (`$response->getData()`) |
| `ERR10022` Satır toplamı tutmuyor | `items` toplamı `amount` ile aynı olmalı |
| `ERR10048` / `ERR10117` Sipariş no kullanılmış | Her ödeme denemesinde yeni bir `transactionId` verin |
| `ERR10115` Desteklenmeyen taksit | `allowedInstallments` hesabınızda tanımlı olmayan bir taksit içeriyor |

## 2.x'ten geçiş

3.0, Omnipay v3 için baştan yazıldı:

- Kart numarasıyla doğrudan satış ve DirectPost kaldırıldı; ödeme ortak ödeme sayfasında alınır.
- `merchant`, `merchantUser`, `merchantPassword`, `secretKey`, `platform` ayarları; `bank` ve `mode` kalktı.
- `completePurchase()` dönüşü imza ve sorguyla doğrular.
- Eski sürümü (2.x) kullanmaya devam etmek isterseniz: `composer require yasinkuyu/omnipay-paratika:^2.0`

## Testler

```bash
composer install
vendor/bin/phpunit
```

## Lisans

MIT — [Yasin Kuyu](https://github.com/yasinkuyu)
