@php
    $moduller = [
        'dashboard' => ['bi-grid-1x2-fill', 'Sistem Yönetim Merkezi', 'Sistem akışını, firma ve şube durumlarını, servis operasyonlarını tek noktadan takip edin.', 'blue'],
        'musteriler.*' => ['bi-people-fill', 'Müşteri Yönetimi', 'Müşteri kartları, iletişim bilgileri, doğum günü ve araç ilişkilerini yönetin.', 'purple'],
        'araclar.*' => ['bi-car-front-fill', 'Araç Yönetimi', 'Araç kartları, plaka, kilometre, QR erişimi ve servis geçmişini yönetin.', 'blue'],
        'servis.kabul*' => ['bi-clipboard2-check-fill', 'Araç Kabul ve Servis Girişi', 'Araç kabulünü tamamlayın; ardından servis çalışma ekranından işlemleri başlatın.', 'orange'],
        'servisler.*' => ['bi-tools', 'Servis ve İş Emirleri', 'Hasar tespiti, işçilik, değişen parçalar, fotoğraflar ve teslim akışını yönetin.', 'teal'],
        'servis.islem*' => ['bi-wrench-adjustable-circle-fill', 'Servis Çalışma Ekranı', 'İşlem, parça, hasar, fotoğraf ve maliyet yönetimini aynı iş emrinde tamamlayın.', 'teal'],
        'ticari.*' => ['bi-receipt-cutoff', 'Ticari ve Muhasebe Yönetimi', 'Cari, teklif, fatura ve mali işlem süreçlerini tek merkezden takip edin.', 'purple'],
        'depo.*' => ['bi-box-seam-fill', 'Depo, Raf ve OEM Parça Kütüphanesi', 'Her parça OEM numarasıyla kaydedilir; stok giriş–çıkışları ve düşük stoklar takip edilir.', 'teal'],
        'kullanicilar.*' => ['bi-person-badge-fill', 'Personel Yönetimi', 'Personel, rol, firma ve şube bağlantılarını kurumsal yapı içinde yönetin.', 'blue'],
        'raporlar.*' => ['bi-bar-chart-line-fill', 'Rapor Merkezi', 'Rolünüze uygun operasyon, maliyet, müşteri ve servis performans özetlerine ulaşın.', 'purple'],
        'ayarlar.*' => ['bi-gear-wide-connected', 'Sistem Ayarları', 'Sistem standartları, bildirimler, QR iletişimi ve yönetim tanımlarını düzenleyin.', 'orange'],
        'sistem.hatalari' => ['bi-shield-exclamation', 'Sistem Hata İzleme', 'Teknik kayıtları inceleyin ve maskelenmiş özetlerle yapay zekâ analiz raporu alın.', 'red'],
        'destek.*' => ['bi-life-preserver', 'Destek Merkezi', 'Talepleri oluşturun, çözüm sürecini ve destek durumunu takip edin.', 'teal'],
        'sohbet.*' => ['bi-chat-square-text-fill', 'Firma İçi İletişim', 'Kullanıcılar ve birimler arasındaki kurumsal iletişimi tek alanda sürdürün.', 'blue'],
        'gelistirme.*' => ['bi-code-square', 'Geliştirme Merkezi', 'Geliştirme taleplerini, onayları ve yapay zekâ önerilerini takip edin.', 'purple'],
    ];
    $bant = ['bi-grid-fill', 'İZGİOS Yönetim Platformu', 'Servis operasyonlarınızı güvenli, izlenebilir ve tek merkezden yönetin.', 'blue'];
    foreach ($moduller as $rota => $veri) { if (request()->routeIs($rota)) { $bant = $veri; break; } }
@endphp
<style>.module-band{display:flex;align-items:center;gap:15px;margin:0 auto 20px;padding:22px 25px;border-radius:19px;color:#fff;background:linear-gradient(115deg,#14213d,#2563eb);box-shadow:0 10px 25px rgba(15,23,42,.1)}.module-band.teal{background:linear-gradient(115deg,#14213d,#0f766e)}.module-band.purple{background:linear-gradient(115deg,#312e81,#7c3aed)}.module-band.orange{background:linear-gradient(115deg,#7c2d12,#ea580c)}.module-band.red{background:linear-gradient(115deg,#450a0a,#b91c1c)}.module-band__icon{display:grid;place-items:center;flex:0 0 50px;width:50px;height:50px;border-radius:14px;background:rgba(255,255,255,.15);font-size:23px}.module-band h1{margin:0;font-size:21px;font-weight:800}.module-band p{margin:6px 0 0;color:rgba(255,255,255,.86);font-size:13px;line-height:1.55}@media(max-width:600px){.module-band{align-items:flex-start;padding:17px}.module-band h1{font-size:18px}.module-band p{font-size:12px}}</style>
<section class="module-band {{ $bant[3] }}"><div class="module-band__icon"><i class="bi {{ $bant[0] }}"></i></div><div><h1>{{ $bant[1] }}</h1><p>{{ $bant[2] }}</p></div></section>
