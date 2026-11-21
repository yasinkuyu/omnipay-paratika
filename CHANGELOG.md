# Değişiklikler

## 3.0.0

- Omnipay v3 (`omnipay/common` ^3.3), PHP 8.1+
- Ortak ödeme sayfası (HPP) akışı: SESSIONTOKEN → `/payment/{oturum}`; kart bilgisi sunucudan geçmez
- Dönüşte `sdSha512` imzası ve QUERYTRANSACTION ile doğrulama, tutar karşılaştırması
- VakıfPayS ve ZiraatPay (`platform`), güncel API adresleri
- Taksit seçenekleri (`allowedInstallments`), sepet satırları (ORDERITEMS)
- İade, iptal, işlem sorgusu; kart numarasıyla doğrudan satış kaldırıldı
