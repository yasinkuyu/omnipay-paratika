# Omnipay: Paratika

**[Omnipay](https://github.com/thephpleague/omnipay) driver for the Paratika, VakıfPayS and ZiraatPay hosted payment page**

[![Latest Stable Version](https://poser.pugx.org/yasinkuyu/omnipay-paratika/v/stable)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)
[![Total Downloads](https://poser.pugx.org/yasinkuyu/omnipay-paratika/downloads)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)
[![License](https://poser.pugx.org/yasinkuyu/omnipay-paratika/license)](https://packagist.org/packages/yasinkuyu/omnipay-paratika)

[Türkçe](README.md) | **English**

## What does this package do?

It lets your shop take card payments. Your server opens a **payment session** at Paratika, the customer goes to
Paratika's **secure payment page**, enters the card and picks installments there, and comes back to your site. Card
data never touches your server.

```
Your server ──(open session)──▶ Paratika API ──▶ session token
Customer ──▶ Paratika payment page (card + installments + 3D Secure) ──▶ Your site (return URL)
Your server ──(transaction query)──▶ Paratika API ──▶ payment confirmed
```

**One platform, three products** (`platform` setting):

| `platform` | Product | Provider |
|---|---|---|
| `paratika` | Paratika | Payten; installment campaigns of many banks in one account |
| `vakifpay` | VakıfPayS | Vakıfbank |
| `ziraatpay` | ZiraatPay | Ziraat Bankası |

**Features**

| | |
|---|---|
| Payment | Hosted payment page (HPP), 3D Secure at the provider |
| Installments | The customer picks them on the provider's page; you decide which ones are offered |
| Refund / void | `refund()` (full or partial), `void()` (before end-of-day) |
| Status | `fetchTransaction()` |
| Security | `sdSha512` signature on return + server-side transaction query + amount check |

## Installation

```bash
composer require yasinkuyu/omnipay-paratika php-http/guzzle7-adapter
```

Requires PHP 8.1 or later.

## What you need from the provider

| Setting | What is it? | Where to find it |
|---|---|---|
| `merchant` | Merchant number | Account opening details / admin panel |
| `merchantUser` | E-mail address of the API user | Create a user **with API permission** in the admin panel |
| `merchantPassword` | Password of the API user | Same user |
| `secretKey` | Merchant secret key | Admin panel → merchant settings. Optional but **recommended** |

The provider gives you a separate merchant account for the test environment; there is no shared public test account.

## Quick start

### 1. Set up the gateway

```php
use Omnipay\Omnipay;

$gateway = Omnipay::create('Paratika');
$gateway->initialize([
    'platform'         => 'paratika',        // paratika | vakifpay | ziraatpay
    'merchant'         => '10000000',
    'merchantUser'     => 'api@shop.com',
    'merchantPassword' => '********',
    'secretKey'        => '********',
    'testMode'         => true,              // false in production
]);
```

### 2. Start the payment (pay.php)

```php
$response = $gateway->purchase([
    'amount'              => '149.90',
    'currency'            => 'TRY',
    'transactionId'       => 'ORD-10045',                       // your order number, unique per attempt
    'returnUrl'           => 'https://shop.com/pay-return.php',
    'clientIp'            => $_SERVER['REMOTE_ADDR'],
    'allowedInstallments' => [2, 3, 6],                         // installments to offer (2-12)
    'card'  => [                                                // NO card number; customer details only
        'email'            => 'ali@example.com',
        'billingFirstName' => 'Ali',
        'billingLastName'  => 'Kaya',
        'billingPhone'     => '5551112233',
    ],
    'items' => [                                                // optional; must add up to amount
        ['name' => 'Sweater', 'quantity' => 2, 'price' => '50.00'],
        ['name' => 'Shipping', 'quantity' => 1, 'price' => '49.90'],
    ],
])->send();

if ($response->isRedirect()) {
    $response->redirect();            // takes the customer to https://…/payment/{session}
} else {
    echo $response->getMessage();     // e.g. "Geçersiz kullanıcı bilgileri" (invalid user credentials)
}
```

If the items don't add up to the amount, the package sends a single line ("Siparis ORD-10045"); the provider requires
the total to match.

### 3. Verify the return (pay-return.php)

```php
$result = $gateway->completePurchase([
    'transactionId' => 'ORD-10045', // the order number from YOUR records (never read from the return)
    'amount'        => '149.90',    // the amount from YOUR records
])->send();

if (!$result->isVerified()) {
    // Not confirmed: bad signature, the query failed, or the transaction is still in progress.
    // Don't mark the order paid; check again later with fetchTransaction().
} elseif ($result->isSuccessful()) {
    // Payment received.
    $pgTranId = $result->getTransactionReference(); // keep it for refund / void
    $result->getInstallment();
    $result->getMaskedCard();                       // 545616******5454
} else {
    echo $result->getMessage();                     // decline reason
}
```

> **How does verification work?** First the `sdSha512` signature on the return is checked with `secretKey`. Then the
> package asks Paratika server-side using the order number you passed. The payment isn't considered successful until an
> approved sale with the right amount is seen. That's why the return page makes an API call.

## Refund, void, status

```php
// Before end-of-day: void
$gateway->void(['transactionReference' => $pgTranId])->send();

// Full or partial refund
$gateway->refund(['transactionReference' => $pgTranId, 'amount' => '50.00', 'currency' => 'TRY'])->send();

// Transactions
$gateway->fetchTransaction(['transactionId' => 'ORD-10045'])->send()->getTransactions();
```

Instead of `transactionReference` (Paratika transaction number) you can also pass `transactionId` (your order number).

Every response has `isSuccessful()` and `getMessage()`. **If `isReadable()` returns false**, the response could not be
read: the operation may have gone through. Do not retry automatically; check the admin panel.

## Other settings

| Setting | Default | Description |
|---|---|---|
| `language` | `tr` | Language of the payment page |
| `sessionExpiry` | provider default (7 days) | Session lifetime, e.g. `1h` |
| `endpoint` | | API URL for an installation not in the list (`https://…/api/v2`) |

## Troubleshooting

| Error | Cause |
|---|---|
| `ERR10020` invalid user credentials | Wrong `merchantUser` / `merchantPassword`, or test and production mixed up |
| `ERR10010` missing required parameter | See the `violatorParam` field of the error (`$response->getData()`) |
| `ERR10022` items don't add up | `items` must add up to `amount` |
| `ERR10048` / `ERR10117` order number already used | Pass a new `transactionId` on every payment attempt |
| `ERR10115` unsupported installment | `allowedInstallments` contains an installment not defined on your account |

## Upgrading from 2.x

3.0 is a rewrite for Omnipay v3:

- Direct sales with a card number and DirectPost were removed; payment is taken on the hosted payment page.
- New settings `merchant`, `merchantUser`, `merchantPassword`, `secretKey`, `platform`; `bank` and `mode` were removed.
- `completePurchase()` verifies the return with the signature and a query.
- To keep using the old version (2.x): `composer require yasinkuyu/omnipay-paratika:^2.0`

## Tests

```bash
composer install
vendor/bin/phpunit
```

## License

MIT — [Yasin Kuyu](https://github.com/yasinkuyu)
