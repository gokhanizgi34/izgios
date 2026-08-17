<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IletisimAyarController extends Controller
{
    public function index(Request $request)
    {
        $firmaId = $this->firmaId($request);
        $vars = $this->mesajAkislari();
        $ayarlar = DB::table('firma_iletisim_kanal_ayarlari')
            ->where('firma_id', $firmaId)
            ->get()
            ->keyBy('mesaj_grubu');
        $loglar = DB::table('iletisim_gonderim_loglari')
            ->where('firma_id', $firmaId)
            ->latest()
            ->limit(20)
            ->get();
        $firmalar = auth()->user()->tamSistemYetkisiVarMi()
            ? Firma::where('aktif', true)->orderBy('unvan')->get()
            : Firma::whereKey($firmaId)->get();

        return view('ayarlar.iletisim-merkezi', compact('firmaId', 'firmalar', 'vars', 'ayarlar', 'loglar'));
    }

    public function kaydet(Request $request)
    {
        $firmaId = $this->firmaId($request);

        foreach ($this->mesajAkislari() as $grup => $akim) {
            DB::table('firma_iletisim_kanal_ayarlari')->updateOrInsert(
                ['firma_id' => $firmaId, 'mesaj_grubu' => $grup],
                [
                    'aktif' => $request->boolean($grup.'_aktif'),
                    'whatsapp' => $request->boolean($grup.'_whatsapp'),
                    'sms' => $request->boolean($grup.'_sms'),
                    'email' => $request->boolean($grup.'_email'),
                    'gonderim_saati' => $request->input($grup.'_saat') ?: null,
                    'sablon' => trim((string) $request->input($grup.'_sablon')) ?: null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Tüm iletişim akışı tercihleri kaydedildi.');
    }

    private function firmaId(Request $request): int
    {
        abort_unless(auth()->check() && (auth()->user()->tamSistemYetkisiVarMi() || auth()->user()->isAdmin()), 403);

        $firmaId = auth()->user()->tamSistemYetkisiVarMi()
            ? ($request->integer('firma_id') ?: Firma::where('aktif', true)->value('id'))
            : auth()->user()->firmaPersoneli?->firma_id;

        abort_unless($firmaId, 403);

        return (int) $firmaId;
    }

    private function mesajAkislari(): array
    {
        return [
            'randevu_olustuldu' => ['Randevu oluşturuldu', 'Müşteriye randevu tarihi, saati, şubesi ve servis bilgisi iletilir.', 'bi-calendar-plus-fill', 'Merhaba {musteri_adi}, {plaka} için randevunuz {randevu_tarihi} {randevu_saati} tarihinde oluşturuldu.'],
            'randevu_yaklasiyor' => ['Yaklaşan randevu', 'Planlanan randevu öncesinde otomatik hatırlatma gönderilir.', 'bi-calendar-event-fill', '{musteri_adi}, {plaka} aracınızın randevusu yaklaşıyor: {randevu_tarihi} {randevu_saati}.'],
            'randevu_iptal' => ['Randevu iptali', 'İptal veya tarih değişikliği müşteriye açık biçimde bildirilir.', 'bi-calendar-x-fill', '{musteri_adi}, {plaka} için randevunuz iptal edildi. Yeni randevu için {firma_adi} ile iletişime geçebilirsiniz.'],
            'servis_kabul' => ['Araç servis kabul edildi', 'Araç kabul kaydı, servis numarası ve ilk durum bilgisi paylaşılır.', 'bi-car-front-fill', '{musteri_adi}, {plaka} aracınız servise kabul edildi. Servis numaranız: {servis_no}.'],
            'teklif_hazir' => ['Teklif hazırlandı', 'Onay bekleyen teklif ve müşteri görüntüleme bağlantısı iletilir.', 'bi-file-earmark-text-fill', '{musteri_adi}, {plaka} için servis teklifiniz hazır. İncelemek için {teklif_linki}.'],
            'islem_basladi' => ['İşlem başladı', 'İş emri üzerinde çalışmanın başladığı bildirilir.', 'bi-wrench-adjustable-circle-fill', '{musteri_adi}, {plaka} aracınızın servis işlemleri başladı.'],
            'ek_islem' => ['Ek işlem / onay', 'Ustanın tespit ettiği ek işlem veya parça ihtiyacı için müşteri onayı istenir.', 'bi-exclamation-diamond-fill', '{musteri_adi}, {plaka} için ek işlem onayı gerekiyor. Detaylar: {ek_islem_detayi}.'],
            'teslimata_hazir' => ['Araç teslimata hazır', 'İşlem tamamlandığında teslim ve varsa bakiye bilgisi iletilir.', 'bi-check2-circle', '{musteri_adi}, {plaka} aracınız teslimata hazır. {firma_adi} ile teslim saatinizi planlayabilirsiniz.'],
            'teslim_edildi' => ['Araç teslim edildi', 'Teslim sonrası teşekkür, evrak bağlantısı ve yeni bakım döngüsü paylaşılır.', 'bi-hand-thumbs-up-fill', '{musteri_adi}, {plaka} aracınız teslim edildi. Bizi tercih ettiğiniz için teşekkür ederiz.'],
            'bakim_hatirlatma' => ['Tarih bazlı bakım hatırlatması', 'Tanımlı bakım tarihinden önce müşteriye hatırlatma yapılır.', 'bi-bell-fill', '{musteri_adi}, {plaka} için planlanan bakım tarihiniz yaklaşıyor: {bakim_tarihi}.'],
            'ozel_gunler' => ['Doğum günü, bayram ve Cuma mesajları', 'Müşteri ve personel özel gün kutlamalarının gönderim kanalı seçilir.', 'bi-stars', '{musteri_adi}, {firma_adi} ailesi olarak güzel günler dileriz.'],
            'servis_evraklari' => ['Servis evrakları', 'Kabul formu, teklif, iş emri özeti, fotoğraflar, fatura ve teslim formu gönderilir.', 'bi-file-earmark-pdf-fill', '{musteri_adi}, {plaka} aracınıza ait servis evraklarınız hazır: {evrak_linki}.'],
        ];
    }
}
