<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SistemHataIzlemeServisi
{
    public function tara(): array
    {
        if (! Schema::hasTable('sistem_hata_durumlari')) return ['acik'=>0,'cozulen'=>0];
        $hatalar = $this->logHatalari();
        $kodlar = $hatalar->pluck('kod')->all();
        foreach ($hatalar as $hata) {
            $mevcut = DB::table('sistem_hata_durumlari')->where('hata_kodu', $hata['kod'])->first();
            DB::table('sistem_hata_durumlari')->updateOrInsert(['hata_kodu'=>$hata['kod']], [
                'seviye'=>$hata['seviye'],'ekran'=>$hata['ekran'],'islem'=>$hata['islem'],'sebep'=>$hata['sebep'],'durum'=>'acik',
                'ilk_goruldu_at'=>$mevcut?->ilk_goruldu_at ?: $hata['zaman'],'son_goruldu_at'=>$hata['zaman'],'updated_at'=>now(),'created_at'=>$mevcut?->created_at ?: now(),
            ]);
        }
        $cozulenler = DB::table('sistem_hata_durumlari')->where('durum','acik')->when($kodlar !== [], fn($q)=>$q->whereNotIn('hata_kodu',$kodlar))->where('son_goruldu_at','<',now()->subMinutes(10))->get();
        foreach ($cozulenler as $kayit) {
            app(SilmeDenetimServisi::class)->tabloKaydiSilindi('SistemHatasi', array_merge((array)$kayit, ['kontrol_notu'=>'Sistem hatası çözüldü']), null);
            DB::table('sistem_hata_durumlari')->where('id',$kayit->id)->delete();
        }
        return ['acik'=>DB::table('sistem_hata_durumlari')->where('durum','acik')->count(),'cozulen'=>$cozulenler->count()];
    }

    public function acikHatalar(): Collection
    {
        return DB::table('sistem_hata_durumlari')->where('durum','acik')->latest('son_goruldu_at')->limit(100)->get()->map(fn($h)=>[
            'kod'=>$h->hata_kodu,'seviye'=>$h->seviye,'zaman'=>$h->son_goruldu_at,'ekran'=>$h->ekran,'islem'=>$h->islem,'sebep'=>$h->sebep,
        ]);
    }

    private function logHatalari(): Collection
    {
        $yol=storage_path('logs/laravel.log'); if(!File::exists($yol)) return collect();
        $dosya=fopen($yol,'rb'); fseek($dosya,max(0,File::size($yol)-1048576)); $icerik=stream_get_contents($dosya)?:''; fclose($dosya);
        return collect(preg_split('/\R(?=\[\d{4}-\d{2}-\d{2} )/',$icerik))->filter(fn($k)=>preg_match('/\.(ERROR|CRITICAL|ALERT|EMERGENCY):/i',$k)===1)->map(function($kayit){
            preg_match('/^\[([^\]]+)\].*?\.(ERROR|CRITICAL|ALERT|EMERGENCY):\s*(.*)$/si',$kayit,$e); $seviye=strtoupper($e[2]??'ERROR'); $mesaj=trim(preg_replace('/\s+/',' ',strtok($e[3]??$kayit,"\n")?:$kayit));
            $ekran='Sistem işlemi'; $islem='Uygulama akışı sırasında işlem'; foreach(['KullaniciController'=>'Personel','MusteriController'=>'Müşteri','FirmaYonetimController'=>'Firma yönetimi','ServisKabulController'=>'Servis kabul','ServisController'=>'İş emirleri','DepoController'=>'Depo','TicariController'=>'Muhasebe ve entegrasyon'] as $sinif=>$ad) if(str_contains($kayit,$sinif)){$ekran=$ad;$islem=$ad.' ekranındaki işlem';break;}
            $kod='HATA-'.$seviye.'-'.strtoupper(substr(sha1($mesaj),0,10)); return ['kod'=>$kod,'seviye'=>$seviye,'zaman'=>$e[1]??now(),'ekran'=>$ekran,'islem'=>$islem,'sebep'=>mb_strimwidth($mesaj,0,600,'…','UTF-8')];
        })->filter(function(array $hata){
            try { return Carbon::parse($hata['zaman'])->greaterThanOrEqualTo(now()->subMinutes(10)); }
            catch (\Throwable) { return true; }
        })->unique('kod')->values();
    }
}
