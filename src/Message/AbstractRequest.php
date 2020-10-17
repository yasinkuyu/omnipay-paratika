<?php

/*
Omnipay-Paratika

Paratika (Asseco) MOTO/3D gateway for Omnipay payment processing library

İnsya Bilişim Teknolojileri
http://insya.com

@yasinkuyu
07.06.2017
*/

namespace Omnipay\Paratika\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

    public function getEndpoints() {
            
        return array(

            'api'                    => 'https://vpos.paratika.com.tr/paratika/api/v2',
            'DirectPost3D'           => 'https://vpos.paratika.com.tr/paratika/api/v2/post/sale3d/{sessionToken}',
            'NonDirectPost3D'        => 'https://vpos.paratika.com.tr/merchant/post/sale/{sessionToken}',

            'api_test'               => 'https://test.paratika.com.tr/paratika/api/v2',
            'DirectPost3DUrl_test'   => 'https://test.paratika.com.tr/paratika/api/v2/post/sale3d/{sessionToken}',
            'NonDirectPost3D_test'   => 'https://test.paratika.com.tr/merchant/post/sale/{sessionToken}',

            'Merchant_test'          => 'https://test.paratika.com.tr/merchant/payment/{sessionToken}',
            
            'test' => ' https://entegrasyon.paratika.com.tr/'

        );

    }

    public function getErrors() {

        return array(
            "ERR10010" =>   "İstekte zorunlu parametrelerden biri bulunamadı",
            "ERR10011" =>   "Aynı parametre bir defadan fazla olarak gönderilmiş",
            "ERR10012" =>   "Bu değer için azami büyüklük değeri aşıldı.",
            "ERR10013" =>   "Bu değer için geçersiz veri tipi belirtilmiş",
            "ERR10014" =>   "Geçersiz güvenlik algoritması belirtilmiş",
            "ERR10015" =>   "Geçersiz üye iş yeri bilgisi belirtilmiş",
            "ERR10016" =>   "Geçersiz tutar bilgisi belirtilmiş",
            "ERR10017" =>   "Geçersiz para birimi belirtilmiş",
            "ERR10018" =>   "Geçersiz dil seçimi",
            "ERR10019" =>   "Genel hata",
            "ERR10020" =>   "Geçersiz kullanıcı bilgileri",
            "ERR10021" =>   "Boş parametre belirtilmiş, tüm parametreleri kontrol edin",
            "ERR10022" =>   "Sipariş edilen ürünlerin toplam tutarı gerçek tutarla örtüşmüyor",
            "ERR10023" =>   "Ödeme tutarı hesaplanan tutarla örtüşmüyor",
            "ERR10024" =>   "Geçersiz vergi tutarı belirtilmiş",
            "ERR10025" =>   "Belirtilen durumda vergi tutarı sıfır olmalıdır",
            "ERR10026" =>   "Geçersiz entegrasyon modeli belirtilmiş",
            "ERR10027" =>   "Geçersiz kart bilgisi (TOKEN) belirtilmiş",
            "ERR10028" =>   "Belirtilen ödeme sistemi (sanal POS) bulunamadı",
            "ERR10029" =>   "Belirtilen ödeme tipi (kampanya) bulunamadı",
            "ERR10030" =>   "Belirtilen işlem bulunamadı",
            "ERR10031" =>   "Bu işlem iade edilemez",
            "ERR10032" =>   "Geçersiz iade tutarı belirtilmiş ya da bu işlem daha önce iade edilmiş",
            "ERR10033" =>   "Bu işlem iptal edilemez",
            "ERR10034" =>   "Belirtilen ödeme bulunamadı",
            "ERR10035" =>   "Bu işlem için ön otorizasyon kaydı bulunmamaktadır",
            "ERR10036" =>   "Geçersiz son otorizasyon (POSTAUTH) tutarı belirtilmiş",
            "ERR10037" =>   "Belirtilen Kart Sahibi (Müşteri) kayıtlı değil",
            "ERR10038" =>   "İlgili ödeme onay beklemektedir",
            "ERR10039" =>   "Geçersiz ödeme durumu belirtilmiş",
            "ERR10040" =>   "Geçersiz alt işlem (SUBACTION) belirtilmiş",
            "ERR10041" =>   "Belirtilen kart daha önce eklenmiş",
            "ERR10042" =>   "Kart daha önceden silinmiş",
            "ERR10043" =>   "Geçersiz zaman aralığı belirtilmiş",
            "ERR10044" =>   "Geçersiz tarih formatı belirtilmiş",
            "ERR10045" =>   "Belirtilen kart numarası geçersizdir",
            "ERR10046" =>   "Belirtilen kredi kartı geçerlilik tarihi geçersizdir",
            "ERR10047" =>   "Kullanıcının API servislerini kullanma yetkisi bulunmamaktadır",
            "ERR10048" =>   "Başarılı Bir İşlem zaten bu üye iş yeri sipariş numarası ile var",
            "ERR10049" =>   "Geçersiz üye iş yeri grup numarası",
            "ERR10050" =>   "Geçersiz HASH değeri",
            "ERR10051" =>   "Herhangi bir ödeme sistemi (sanal pos) tanımı yok. Lütfen, kontrol ediniz.",
            "ERR10052" =>   "Desteklenmeyen para birimi:",
            "ERR10053" =>   "Kullanıcının bu üye iş yeri üzerinde işlem yapma yetkisi yok",
            "ERR10054" =>   "Ödeme geçerlilik süresi maksimum limitin üstündedir.",
            "ERR10055" =>   "Ödeme geçerlilik süresi minimum limitin altındadır.",
            "ERR10056" =>   "Geçersiz API isteği belirtilmiş",
            "ERR10057" =>   "Kart BIN bilgisi geçersiz",
            "ERR10058" =>   "Kart daha önce etkinleştirilmiş",
            "ERR10059" =>   "Kart daha önce kullanım dışı bırakılmış",
            "ERR10060" =>   "Geçersiz IP Adresi",
            "ERR10062" =>   "Belirtilen kart henüz aktive edilmemiştir.",
            "ERR10063" =>   "Bu işlem sadece LetsBodrum kart ile yapılabilir.",
            "ERR10064" =>   "Lütfen LetsBodrum kart veya Türkiye İş Bankası kredi kartı kullanınız.",
            "ERR10065" =>   "Belirtilen kart numarası daha önceden tanımlanmış.",
            "ERR10066" =>   "Belirtilen zaman bilgisi geçersiz ya da tutarsızdır",
            "ERR10067" =>   "Belirtilen period değeri çok yüksek",
            "ERR10068" =>   "Geçersiz tekrar düzeni parametresi",
            "ERR10069" =>   "Zamanlayıcı (Quartz) hatası oluştu",
            "ERR10070" =>   "Başlangı tarihi gelecekteki bir tarih olmalıdır",
            "ERR10071" =>   "Geçersiz tekrarlı ödeme durum parametresi belirtilmiş",
            "ERR10072" =>   "Tekrarlı ödeme planı zaten etkin durumda",
            "ERR10073" =>   "Tekrarlı ödeme planı zaten kullanım dışı",
            "ERR10074" =>   "Tekrarlı ödeme planının zaten süresi geçmiş",
            "ERR10075" =>   "Üye iş yeri görsel (logo) bilgisi hatalı",
            "ERR10076" =>   "Geçersiz tekrarlı ödeme durum parametresi",
            "ERR10078" =>   "İşlem kilitlidir",
            "ERR10079" =>   "Bu kart sistemde kayıtlıdır.",
            "ERR10080" =>   "Lütfen, Üye İş Yeri Sipariş numarasını veya Ödeme Oturumu(Token) veriniz",
            "ERR10081" =>   "Geçersiz işlem durumu",
            "ERR10082" =>   "Kullanıcısının bu işlem için yetkisi yoktur.",
            "ERR10083" =>   "Geçersiz statü",
            "ERR10084" =>   "Faiz veya indirim oranı sıfır değeri olmalıdır",
            "ERR10085" =>   "Geçerli bitiş tarihi ,geçerli başlangıç tarihten daha büyük olamaz",
            "ERR10086" =>   "Geçerli bitiş tarihi şimdiki tarihten daha büyük olmalıdır",
            "ERR10087" =>   "Taksit sayı numarası zaten bu ödeme sistemi ile bir ödeme tipi var",
            "ERR10088" =>   "Taksit bilgisi 1-12 arasında bir değer olmalıdır.",
            "ERR10090" =>   "İşlem başarısız",
            "ERR10091" =>   "Ödeme sistemi devre dışı bırakıldığı için işlem gerçekleştiremiyor. Lütfen Üye İş Yeri Süper Yöneticisiyle iletişime geçiniz.",
            "ERR10092" =>   "Geçersiz offset değeri",
            "ERR10093" =>   "Geçersiz limit değeri",
            "ERR10094" =>   "Tanımlı bir kart bulunamadı.",
            "ERR10095" =>   "Kayıtlı bulunan tekrarlayan ödeme planlarından dolayı kart silinemez.",
            "ERR10096" =>   "Geçersiz oturum (session) bilgisi.",
            "ERR10097" =>   "Sonlandırılmış oturum (session) bilgisi.",
            "ERR10098" =>   "Bu oturum anahtarının yapılmak istenen işleme yetkisi yoktur.",
            "ERR10099" =>   "Bu işlem başka bir üye iş yerine ait.",
            "ERR10100" =>   "Bu ödeme için birden fazla başarılı işlem vardır. Lütfen PGTRANID parametresini kullanınız.",
            "ERR10101" =>   "Geçersiz URL parametresi belirtilmiştir.",
            "ERR10102" =>   "Geçersiz BIN değeri belirtilmiştir.",
            "ERR10103" =>   "İşlem isteği Inact RT servisi tarafından raporlanan fraud olasılığı nedeniyle reddedilmiştir.",
            "ERR10104" =>   "Kullanılabilir komisyon şeması bulunmamaktadır.",
            "ERR10105" =>   "Mevcut Ödeme Sistemi havuzda bulunmamaktadır",
            "ERR10106" =>   "İşlem tutarı üye iş yeri hesabına geçmemiştir, iade yapılamaz.",
            "ERR10107" =>   "Bu ödeme zaten yapılmıştır, verilen Üye İş Yeri Sipariş Numarası ile yeni ödeme oturumu oluşturulamaz.",
            "ERR10108" =>   "Üye iş yeri onaylanmamış",
            "ERR10109" =>   "Ödeme havuzu üye iş yeri için henüz onaylanmamıştır.",
            "ERR10110" =>   "Kullanilan ödeme sistemi kampanya kullanımını desteklememektedir.",
            "ERR10111" =>   "Puan sorgulama ödeme sistemi tarafından desteklenmemektedir.",
            "ERR10112" =>   "Hatali puan formatı lütfen API Dokümantasyonundan puan kullanım formatını kontrol ediniz.",
            "ERR10113" =>   "Kullanilan ödeme sistemi puan kullanımını desteklememektedir.",
            "ERR10115" =>   "Üye iş yeri tarafından desteklenmeyen taksit sayısı belirtilmiştir.",
            "ERR10116" =>   "Bu işlem kullanımda olmayan üye iş yeri bilgileriyle gerçekleştirilemez.",
            "ERR10117" =>   "Bu sipariş numarası sonlanan bir oturumda kullanılmıştır lütfen farklı bir sipariş numarası ile oturum anahtarı oluşturun.",
            "ERR10118" =>   "İstek ile mevcut sipariş numarasına ait oturumun tutar,kur,oturum tipi, url dönüş değeri ya da yapılmak istenen işlem değerlerinden biri uyuşmamaktadır.",
            "ERR10119" =>   "Tam ve ya noktalı kısımda limit aşımı",
            "ERR10120" =>   "Bu plan koduna ait bir tekrarlı ödeme bulunuyor",
            "ERR10121" =>   "Geçersiz tekrarlı ödeme kodu",
            "ERR10122" =>   "Sonlanmış durumdaki tekrarlı ödeme güncellenemez.",
            "ERR10123" =>   "Geçersiz işlem tipi",
            "ERR10125" =>   "Mutabakat sorgusu için en az bir parametre geçilmeli.",
            "ERR10126" =>   "Birden fazla işlem bulundu.",
            "ERR10127" =>   "Ãdeme sistemi puan parametresi hatalÄ±, işlemin gÃ¶nderileceÄi Ã¶deme sisteminde gÃ¶nderilen puan parametresi tanımlı değildir.",
            "ERR10128" =>   "Geçersiz parametre değeri",
            "ERR10129" =>   "Parçalı puan kullanımı bu ödeme sistemi tarafından desteklenmemektedir",
            "ERR10130" =>   "İşlem fraud süphesiyle reddedilmiştir. Detaylı bilgi için destek ekibiyle iletişime geçebilirsiniz. (TMX rejected)",
            "ERR10131" =>   "Belirtilen tokena ait işlem bulundu.",
            "ERR10132" =>   "Aranan BKM İşlemi bulunamadı",
            "ERR10133" =>   "İstenen işlem güncellenemez.",
            "ERR10134" =>   "Ödeme sistemi tipi ya da EFT kodu bulunamadı.",
            "ERR10135" =>   "EXTRA parametresi decode edilemiyor.",
            "ERR10136" =>   "Bu üye iş yeri için ortak ödeme sayfası (HPP) kullanılamaz.",
            "ERR10137" =>   "Query Campaign Not Supported By PaymentSystem",
            "ERR10138" =>   "3D işlem yaparken hata oluştu.",


            "ERR20001" =>   "Manuel onay için bankanızla iletişime geçiniz",
            "ERR20002" =>   "Sahte onay, bankanızla iletişime geçiniz",
            "ERR20003" =>   "Geçersiz üye iş yeri ya da servis sağlayıcı",
            "ERR20004" =>   "Karta el koyunuz",
            "ERR20005" =>   "İşleme onay verilmedi",
            "ERR20006" =>   "Hata (Sanal POS ya da banka tarafında sadece kayıt güncelleme cevapları bulundu)",
            "ERR20007" =>   "Karta el koyunuz - Özel nedenler",
            "ERR20008" =>   "Sahte onay, bankanızla iletişime geçiniz",
            "ERR20011" =>   "Sahte onay (VIP), bankanızla iletişime geçiniz",
            "ERR20012" =>   "Sanal POS ya da banka tarafında geçersiz işlem",
            "ERR20013" =>   "Sanal POS hatası: Geçersiz tutar bilgisi",
            "ERR20014" =>   "Geçersiz hesap ya da kart numarası belirtilmiş",
            "ERR20015" =>   "Böyle bir banka (issuer) bulunamadı",
            "ERR20019" =>   "Sanal POS hatası: Tekrar deneyiniz",
            "ERR20020" =>   "Sanal POS hatası: Geçersiz / Hatalı tutar",
            "ERR20021" =>   "Banka / Sanal POS tarafında işlem yapılamıyor",
            "ERR20025" =>   "Sanal POS hatası: Kayıt oluşturulamadı",
            "ERR20026" =>   "Sanal POS tarafında işlem bulunamadı",
            "ERR20027" =>   "Sanal POS hatası: Banka reddetti",
            "ERR20028" =>   "Sanal POS hatası: Original is denied",
            "ERR20029" =>   "Sanal POS hatası: Original not found",
            "ERR20030" =>   "Sanal POS tarafında switch bazlı format hatası",
            "ERR20032" =>   "Sanal POS tarafında genel yönlendirme hatası",
            "ERR20033" =>   "Belirtilen kredi kartının geçerlilik süresi bitmiştir",
            "ERR20034" =>   "İşlemde sahtecilik (fraud) şüphesi",
            "ERR20036" =>   "Sanal POS hatası: Kısıtlanmış kart",
            "ERR20037" =>   "Sanal POS hatası: Banka (Issuer) kartı geri çağrıyor",
            "ERR20038" =>   "Sanal POS hatası: İzin verilen PIN deneme sayısı aşıldı",
            "ERR20040" =>   "Sanal POS hatası: İade işlemi gün sonundan önce yapılamaz",
            "ERR20041" =>   "Sanal POS hatası: Kayıp kart, karta el koyunuz",
            "ERR20043" =>   "Sanal POS hatası: Çalıntı kart, karta el koyunuz",
            "ERR20051" =>   "Belirtilen kredi kartının limiti bu işlem için yeterli değildir",
            "ERR20052" =>   "Sanal POS hatası: Çek hesabı bulunamadı",
            "ERR20053" =>   "Sanal POS hatası: Tasarruf hesabı bulunamadı",
            "ERR20054" =>   "Kartın kullanım süresi geçmiş",
            "ERR20055" =>   "Sanal POS hatası: Hatalı / Geçersiz PIN değeri",
            "ERR20056" =>   "Sanal POS hatası: Kart bilgisi bulunamadı",
            "ERR20057" =>   "Kart sahibine bu işlem yetkisi verilmemiştir",
            "ERR20058" =>   "Terminale bu işlem izni verilmemiştir",
            "ERR20059" =>   "İşlemde sahtecilik (fraud) şüphesi vardır",
            "ERR20061" =>   "Sanal POS hatası: Beklenen işlem tutar sınırı aşıldı",
            "ERR20062" =>   "Belirtilen kredi kartı kısıtlanmıştır",
            "ERR20063" =>   "Sanal POS tarafında güvenlik ihlali durumu",
            "ERR20065" =>   "Sanal POS hatası: Beklenen işlem sınırı aşıldı",
            "ERR20075" =>   "Sanal POS hatası: İzin verilen PIN deneme sayısı aşıldı",
            "ERR20076" =>   "Sanal POS anahtar eşzamanlama hatası",
            "ERR20077" =>   "Sanal POS hatası: Geçersiz / Tutarsız bilgi gönderildi",
            "ERR20080" =>   "Geçersiz tarih bilgisi",
            "ERR20081" =>   "Sanal POS şifreleme hatası",
            "ERR20082" =>   "Geçersiz / Hatalı CVV değeri",
            "ERR20083" =>   "PIN değeri doğrulanamıyor",
            "ERR20084" =>   "Geçersiz / Hatalı CVV değeri",
            "ERR20085" =>   "Sanal POS tarafında reddedildi (Genel)",
            "ERR20086" =>   "Doğrulanamadı",
            "ERR20091" =>   "Banka / Sanal POS şu an işlem gerçekleştiremiyor",
            "ERR20092" =>   "Zaman aşımı nedeniyle teknik iptal gerçekleşitiriliyor",
            "ERR20093" =>   "Taksit ya da puan kullandırma ihlali, işlem tamamlanamıyor",
            "ERR20096" =>   "Sanal POS tarafında genel hata",
            "ERR20098" =>   "Çoklu iptal (Duplicate reversal)",
            "ERR20099" =>   "Lütfen yeniden deneyiniz, sorun devam ederse bankanızla iletişime geçiniz.",
            "ERR200YK" =>   "Kart kara listede bulunuyor",
            "ERR200SF" =>   "Detaylar için sanal POS cevabındaki HOSTMSG alanını kontrol ediniz.",
            "ERR200GK" =>   "Sanal POS hatası: Bu terminalde yanabcı kartlar için yetki bulunmamaktadır.",
        );

    }

    public function getMode() {
        return $this->getParameter('mode');
    }

    public function setMode($value) {
        return $this->setParameter('mode', $value);
    }

    public function getBank() {
        return $this->getParameter('bank');
    }

    public function setBank($value) {
        return $this->setParameter('bank', $value);
    }

    public function getMerchant() {
        return $this->getParameter('merchant');
    }

    public function setMerchant($value) {
        return $this->setParameter('merchant', $value);
    }

    public function getMerchantUser() {
        return $this->getParameter('merchantuser');
    }

    public function setMerchantUser($value) {
        return $this->setParameter('merchantuser', $value);
    }

    public function getMerchantPassword () {
        return $this->getParameter('merchantpassword');
    }

    public function setMerchantPassword ($value) {
        return $this->setParameter('merchantpassword', $value);
    }
  
    public function getCurrency () {
        return $this->getParameter('currency');
    }

    public function setCurrency($value) {
        return $this->setParameter('currency', $value);
    }

    public function getSessionToken () {
        return $this->getParameter('sessionToken');
    }

    public function setSessionToken($value) {
        return $this->setParameter('sessionToken', $value);
    }

    public function getSessionType() {
        return $this->getParameter('sessionType');
    }

    public function setSessionType($value) {
        return $this->setParameter('sessionType', $value);
    }

    public function getPaymentSystem () {
        return $this->getParameter('paymentSystem');
    }

    public function setPaymentSystem($value) {
        return $this->setParameter('paymentSystem', $value);
    }

    public function getSecretKey () {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value) {
        return $this->setParameter('secretKey', $value);
    }

    public function getQueryAction () {
        return $this->getParameter('queryAction');
    }

    public function setQueryAction($value) {
        return $this->setParameter('queryAction', $value);
    }

    public function getOrderId() {
        return $this->getParameter('orderId');
    }

    public function setOrderId($value) {
        return $this->setParameter('orderId', $value);
    }

    public function getInstallment() {
        return $this->getParameter('installment');
    }

    public function setInstallment($value) {
        return $this->setParameter('installment', $value);
    }
    
    public function getBin() {
        return $this->getParameter('Bin');
    }

    public function setBin($value) {
        return $this->setParameter('Bin', $value);
    }

    public function getEndpoint()
    {
         
        $mode = $this->getMode();


        if (!array_key_exists($mode, $this->getEndpoints())) {
            throw new \Exception('Lütfen geçerli bir mod seçiniz');
        }

        if($this->getTestMode()){
            return $this->getEndpoints()["test"];
        }

        $endpoint = $this->getEndpoints()[$mode];

        $sessionToken = $this->getSessionToken();

        // Oturum aç ve sessin token değişkeni ile değiştir.
        if($sessionToken != ""){
            $endpoint = str_replace("{sessionToken}", $sessionToken, $endpoint);
        }

        return $endpoint;

    }

}
