<?php

namespace App\Services;

use App\Models\SilmeDenetimKaydi;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SilmeDenetimServisi
{
    public function modelSilindi(Model $model): void
    {
        if ($model instanceof SilmeDenetimKaydi) return;
        $this->kaydet($model::class, (string) $model->getKey(), $model->getAttributes(), $this->firmaId($model));
    }

    public function tabloKaydiSilindi(string $tur, object|array $veri, ?int $firmaId): void
    {
        $dizi = (array) $veri;
        $this->kaydet($tur, (string) ($dizi['id'] ?? ''), $dizi, $firmaId);
    }

    private function kaydet(string $tur, string $id, array $veri, ?int $firmaId): void
    {
        if (! Schema::hasTable('silme_denetim_kayitlari')) return;
        $kullanici = auth()->user();
        $guvenliVeri = collect($veri)->except(['password','remember_token','api_anahtari_sifreli'])->toArray();
        $kayit = SilmeDenetimKaydi::create([
            'firma_id' => $firmaId,
            'kullanici_id' => $kullanici?->id,
            'modul' => $this->modul($tur),
            'kayit_turu' => class_basename($tur),
            'kayit_id' => $id ?: null,
            'kayit_ozeti' => $this->ozet($guvenliVeri),
            'silinen_veri' => $guvenliVeri,
            'islemi_yapan' => $kullanici?->tamAdi() ?: 'Sistem işlemi',
            'rol' => $kullanici?->rolAdi(),
            'ip_adresi' => request()?->ip(),
            'ekran_adresi' => request()?->fullUrl(),
        ]);
        $this->firmaSahibineBildir($kayit);
    }

    private function firmaSahibineBildir(SilmeDenetimKaydi $kayit): void
    {
        if (! $kayit->firma_id || ! Schema::hasTable('users') || ! Schema::hasTable('firma_personels')) return;
        $alicilar = User::query()->where('role', 'admin')->where('status', 'aktif')->whereNotNull('email')
            ->whereHas('firmaPersoneli', fn ($q) => $q->where('firma_id', $kayit->firma_id)->where('aktif', true))
            ->pluck('email')->filter()->unique()->values();
        if ($alicilar->isEmpty()) return;
        try {
            $entegrasyonAktif = DB::table('muhasebe_entegrasyonlari')->where('firma_id', $kayit->firma_id)->where('saglayici', 'email')->where('aktif', true)->exists();
            if ($entegrasyonAktif) {
                foreach ($alicilar as $alici) {
                    app(FirmaIletisimGonderici::class)->gonder((object) ['firma_id'=>$kayit->firma_id,'kanal'=>'email','alici'=>$alici,'mesaj'=>"{$kayit->modul} kaydı silindi. Kayıt: {$kayit->kayit_ozeti}. İşlemi yapan: {$kayit->islemi_yapan} ({$kayit->rol}). Tarih: {$kayit->created_at->format('d.m.Y H:i')}. IP: {$kayit->ip_adresi}"], 'Silme işlemi bildirimi · '.$kayit->modul);
                }
                $kayit->update(['firma_sahibine_mail' => true]);
                return;
            }
            Mail::send('emails.silme-bildirimi', ['kayit' => $kayit], function ($mail) use ($alicilar, $kayit) {
                $mail->to($alicilar->all())->subject('Silme işlemi bildirimi · '.$kayit->modul);
            });
            $kayit->update(['firma_sahibine_mail' => true]);
        } catch (Throwable $e) {
            report($e);
            $kayit->update(['mail_hatasi' => mb_substr($e->getMessage(), 0, 2000)]);
        }
    }

    private function firmaId(Model $model): ?int
    {
        if ($model->getAttribute('firma_id')) return (int) $model->getAttribute('firma_id');
        if ($model->getTable() === 'firmas') return (int) $model->getKey();
        foreach (['servis_id' => 'servisler', 'arac_id' => 'araclar', 'musteri_id' => 'musteris', 'sube_id' => 'subes'] as $alan => $tablo) {
            if ($model->getAttribute($alan)) return DB::table($tablo)->where('id', $model->getAttribute($alan))->value('firma_id');
        }
        if ($model instanceof User) return $model->firmaPersoneli?->firma_id;
        return null;
    }

    private function modul(string $tur): string
    {
        $ad = mb_strtolower(class_basename($tur));
        return match (true) {
            str_contains($ad, 'servis') => 'Servis', str_contains($ad, 'muster') => 'Müşteri', str_contains($ad, 'arac') => 'Araç',
            str_contains($ad, 'user') || str_contains($ad, 'personel') => 'Personel', str_contains($ad, 'parca') || str_contains($ad, 'stok') || str_contains($ad, 'depo') => 'Depo / Yedek Parça',
            str_contains($ad, 'randevu') => 'Randevu', str_contains($ad, 'firma') => 'Firma', str_contains($ad, 'sube') => 'Şube', default => 'Diğer',
        };
    }

    private function ozet(array $veri): string
    {
        foreach (['servis_no','plaka','ad_soyad','sube_adi','unvan','parca_adi','islem_adi','baslik','name'] as $alan) if (filled($veri[$alan] ?? null)) return (string) $veri[$alan];
        return 'Kayıt #'.($veri['id'] ?? '-');
    }
}
