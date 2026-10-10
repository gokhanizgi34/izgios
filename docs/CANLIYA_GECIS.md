# İZGİOS Canlıya Geçiş Kontrol Listesi

## 1. Sunucu

- Ubuntu 24.04 LTS VPS
- Nginx, MySQL 8, PHP 8.3, Composer 2 ve Node.js 22
- Alan adı: `app.izgios.com` (ve gerekirse müşteri QR bağlantıları için `qr.izgios.com`)
- SSL: Let's Encrypt
- Uygulama dizini: `/var/www/izgios`

## 2. GitHub ve dağıtım akışı

1. Yerel değişiklikler `gelistirme-kullanici-sistemi` dalında commit edilir.
2. Kontrol edilen sürüm `production` dalına alınır.
3. GitHub Actions, sunucuda dağıtım komutunu çalıştırır.
4. Sunucu `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`, `php artisan migrate --force` ve önbellek komutlarını yürütür.
5. Nginx ve PHP-FPM yeniden yüklenir; queue worker kesintisiz çalışır.

Canlı `.env`, API anahtarları, SMTP şifresi ve sunucu erişim bilgileri GitHub'a kesinlikle gönderilmez. GitHub Secrets içinde tutulur.

## 3. İlk kurulum komutları

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`.env.production.example` dosyası canlı sunucuda `.env` olarak kopyalanır ve sunucuya özel değerler girilir.

## 4. Sürekli çalışan işler

- Queue worker: `php artisan queue:work --sleep=3 --tries=3 --timeout=120`
- Zamanlayıcı: her dakika `php artisan schedule:run`
- Laravel logları ve disk alanı için günlük kontrol
- MySQL günlük yedekleme ve en az 30 günlük saklama

## 5. Canlı test sırası

1. Sistem yöneticisi, firma sahibi, usta, muhasebe ve İK rolleriyle giriş.
2. Firma/şube izolasyonu: her kullanıcı yalnız kendi firma verisini görür.
3. Müşteri → araç → servis kabul → iş emri → teslim akışı.
4. QR müşteri ekranı: giriş gerektirmeyen tek istisna olarak doğrulanır.
5. Dosya/fotoğraf yükleme ve QR WhatsApp bağlantısı.
6. E-posta, SMS ve WhatsApp sağlayıcıları önce test hesabı ile doğrulanır.
7. Hata izleme, destek ve yedek geri yükleme senaryosu.

## 6. Geri dönüş planı

Dağıtımdan önce MySQL yedeği alınır. Her dağıtım bir Git commit'ine bağlıdır; sorun halinde önceki çalışan sürüme dönülür ve migration geri alma yalnız yedekten doğrulandıktan sonra yapılır.
