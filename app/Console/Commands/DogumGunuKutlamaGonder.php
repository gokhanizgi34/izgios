<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Musteri;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\MusteriIletisimIzinServisi;

class DogumGunuKutlamaGonder extends Command
{
    protected $signature = 'izgi:dogum-gunu-kutla';
    protected $description = 'Doğum günü gelen personele firma iletişim kanallarından anlık kutlama planlar.';

    public function handle(): int
    {
        $ayarlar=DB::table('yonetim_ayarlari')->where('grup','bildirim')->pluck('deger','anahtar');
        if(isset($ayarlar['dogum_gunu'])&&!filter_var($ayarlar['dogum_gunu'],FILTER_VALIDATE_BOOLEAN)){$this->info('Doğum günü otomasyonu kapalı.');return self::SUCCESS;}
        $saat=(string)($ayarlar['gonderim_saati']??'10:00');
        if(now()->format('H:i')<$saat){$this->info("Gönderim saati bekleniyor: {$saat}.");return self::SUCCESS;}

        $ozelGunPersonelleri=DB::table('ik_ozel_gunler')->where('tur','dogum_gunu')->where('hatirlatma_aktif',true)->whereMonth('tarih',now()->month)->whereDay('tarih',now()->day)->pluck('user_id');
        $personeller=User::query()->where('status','aktif')->whereHas('firmaPersoneli',fn($q)=>$q->where('aktif',true))->where(function($q)use($ozelGunPersonelleri){$q->where(function($tarih){$tarih->whereNotNull('dogum_tarihi')->whereMonth('dogum_tarihi',now()->month)->whereDay('dogum_tarihi',now()->day);})->orWhereIn('id',$ozelGunPersonelleri);})->with('firmaPersoneli.firma')->get();
        $planlanan=0;
        foreach($personeller as $personel){$firmaPersonel=$personel->firmaPersoneli;$firma=$firmaPersonel?->firma;if(!$firma)continue;$kanalAyari=DB::table('firma_iletisim_kanal_ayarlari')->where(['firma_id'=>$firma->id,'mesaj_grubu'=>'ozel_gunler'])->first();if($kanalAyari&&!$kanalAyari->aktif)continue;$sablon=$kanalAyari?->sablon?:'{musteri_adi}, {firma_adi} ailesi olarak doğum gününüzü kutlar; sağlıklı ve mutlu yıllar dileriz.';$mesaj=strtr($sablon,['{musteri_adi}'=>$personel->tamAdi(),'{firma_adi}'=>$firma->unvan]);
            foreach(['whatsapp','sms','email'] as $kanal){$acik=$kanalAyari?(bool)$kanalAyari->{$kanal}:$kanal==='email';if(!$acik)continue;$alici=$kanal==='email'?$personel->email:$personel->phone;if(!$alici)continue;$varMi=DB::table('iletisim_gonderim_loglari')->where('kaynak_turu','personel_dogum_gunu')->where('kaynak_id',$personel->id)->where('firma_id',$firma->id)->where('kanal',$kanal)->whereYear('planlanan_at',now()->year)->exists();if($varMi)continue;DB::table('iletisim_gonderim_loglari')->insert(['firma_id'=>$firma->id,'user_id'=>$personel->id,'mesaj_grubu'=>'ozel_gunler','kanal'=>$kanal,'durum'=>'planlandi','alici'=>$alici,'alici_maskeli'=>$this->maskele($alici,$kanal),'mesaj'=>$mesaj,'planlanan_at'=>now(),'kaynak_turu'=>'personel_dogum_gunu','kaynak_id'=>$personel->id,'created_at'=>now(),'updated_at'=>now()]);$planlanan++;}
        }
        $musteriler=Musteri::query()->whereNotNull('firma_id')->whereNotNull('dogum_tarihi')->whereMonth('dogum_tarihi',now()->month)->whereDay('dogum_tarihi',now()->day)->with('firma')->get();
        $izinServisi=app(MusteriIletisimIzinServisi::class);
        foreach($musteriler as $musteri){$firma=$musteri->firma;if(!$firma||!$firma->aktif||!$izinServisi->izinliMi($firma->id,$musteri->id,'ticari'))continue;$kanalAyari=DB::table('firma_iletisim_kanal_ayarlari')->where(['firma_id'=>$firma->id,'mesaj_grubu'=>'ozel_gunler'])->first();if($kanalAyari&&!$kanalAyari->aktif)continue;$sablon=$kanalAyari?->sablon?:'{musteri_adi}, {firma_adi} ailesi olarak doğum gününüzü kutlar; sağlıklı ve mutlu yıllar dileriz.';$mesaj=strtr($sablon,['{musteri_adi}'=>$musteri->ad_soyad,'{firma_adi}'=>$firma->unvan]);
            foreach(['whatsapp','sms','email'] as $kanal){$acik=$kanalAyari?(bool)$kanalAyari->{$kanal}:$kanal==='email';if(!$acik)continue;$alici=$kanal==='email'?$musteri->email:$musteri->telefon;if(!$alici)continue;$varMi=DB::table('iletisim_gonderim_loglari')->where('kaynak_turu','musteri_dogum_gunu')->where('kaynak_id',$musteri->id)->where('firma_id',$firma->id)->where('kanal',$kanal)->whereYear('planlanan_at',now()->year)->exists();if($varMi)continue;DB::table('iletisim_gonderim_loglari')->insert(['firma_id'=>$firma->id,'musteri_id'=>$musteri->id,'mesaj_grubu'=>'ozel_gunler','kanal'=>$kanal,'durum'=>'planlandi','alici'=>$alici,'alici_maskeli'=>$this->maskele($alici,$kanal),'mesaj'=>$mesaj,'planlanan_at'=>now(),'kaynak_turu'=>'musteri_dogum_gunu','kaynak_id'=>$musteri->id,'created_at'=>now(),'updated_at'=>now()]);$planlanan++;}
        }
        $this->info("{$planlanan} personel/müşteri doğum günü bildirimi anlık kuyruğa alındı.");return self::SUCCESS;
    }

    private function maskele(string $alici,string $kanal):string
    {
        if($kanal==='email'){[$kullanici,$alan]=array_pad(explode('@',$alici,2),2,'');return mb_substr($kullanici,0,2).'***@'.$alan;}
        return strlen($alici)>4?substr($alici,0,3).'****'.substr($alici,-2):'***';
    }
}
