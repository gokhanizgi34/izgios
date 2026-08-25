<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class StokKaynakController extends Controller
{
    public function kaydet(Request $request)
    {
        $firmaId = $this->firmaId($request);
        $veri = $request->validate([
            'ad' => ['required','string','max:120'],
            'adresler' => ['required','array','min:1','max:20'],
            'adresler.*' => ['nullable','url:http,https','max:1000'],
        ]);
        $adresler = array_values(array_filter($veri['adresler'], fn($adres) => filled($adres)));
        abort_if(empty($adresler), 422, 'En az bir ürün kaynağı adresi girin.');
        foreach ($adresler as $sira => $adres) {
            $this->guvenliAdres($adres);
            DB::table('stok_urun_kaynaklari')->updateOrInsert(
                ['firma_id'=>$firmaId,'ad'=>$veri['ad'].' '.($sira+1)],
                ['adres_sifreli'=>Crypt::encryptString($adres),'aktif'=>true,'son_durum'=>'bekliyor','olusturan_id'=>auth()->id(),'updated_at'=>now(),'created_at'=>now()]
            );
        }
        return back()->with('success', count($adresler).' ürün kaynağı güvenli biçimde kaydedildi.');
    }

    public function test(Request $request, int $kaynak)
    {
        $kayit = $this->kaynak($request, $kaynak);
        try {
            $cevap = $this->istek($kayit);
            $urunler = $this->urunListesi($cevap->json());
            DB::table('stok_urun_kaynaklari')->where('id',$kaynak)->update(['son_durum'=>'baglandi','son_urun_sayisi'=>count($urunler),'son_hata'=>null,'updated_at'=>now()]);
            return back()->with('success','Bağlantı başarılı. Kaynakta '.count($urunler).' ürün algılandı.');
        } catch (Throwable $e) {
            $this->hataKaydet($kaynak,$e);
            return back()->withErrors(['kaynak'=>'Bağlantı kurulamadı: '.Str::limit($e->getMessage(),240)]);
        }
    }

    public function senkronizeEt(Request $request, int $kaynak)
    {
        $kayit = $this->kaynak($request, $kaynak);
        try {
            $urunler = $this->urunListesi($this->istek($kayit)->json());
            $eklenen=0; $guncellenen=0; $atlanmis=0;
            foreach (array_chunk($urunler, 250) as $parca) {
                DB::transaction(function () use ($parca,$kayit,&$eklenen,&$guncellenen,&$atlanmis) {
                    foreach ($parca as $ham) {
                        $urun=$this->normalize($ham); if(!$urun){$atlanmis++;continue;}
                        $mevcut=DB::table('stok_parcalar')->where('firma_id',$kayit->firma_id)->where('oem_no',$urun['oem_no'])->first();
                        $urun=$this->benzersizAlanlariHazirla($urun,$kayit,$mevcut);
                        $deger=array_merge($urun,['urun_kaynak_id'=>$kayit->id,'kaynak_senkron_at'=>now(),'updated_at'=>now()]);
                        if($mevcut){DB::table('stok_parcalar')->where('id',$mevcut->id)->update($deger);$guncellenen++;}
                        else{DB::table('stok_parcalar')->insert(array_merge($deger,['firma_id'=>$kayit->firma_id,'stok_miktari'=>0,'minimum_stok'=>0,'alis_fiyati'=>0,'oem_durum'=>'kaynak_dogrulandi','aktif'=>true,'created_at'=>now()]));$eklenen++;}
                    }
                });
            }
            DB::table('stok_urun_kaynaklari')->where('id',$kaynak)->update(['son_durum'=>'basarili','son_urun_sayisi'=>count($urunler),'son_hata'=>null,'son_senkron_at'=>now(),'updated_at'=>now()]);
            return back()->with('success',"Senkronizasyon tamamlandı: {$eklenen} yeni, {$guncellenen} güncel, {$atlanmis} atlanan ürün.");
        } catch (Throwable $e) {
            $this->hataKaydet($kaynak,$e);
            return back()->withErrors(['kaynak'=>'Senkronizasyon başarısız: '.Str::limit($e->getMessage(),240)]);
        }
    }

    private function normalize(array $ham): ?array
    {
        $al=fn(array $adlar,$varsayilan=null)=>collect($adlar)->map(fn($ad)=>data_get($ham,$ad))->first(fn($v)=>$v!==null&&$v!=='') ?? $varsayilan;
        $kod=trim((string)$al(['oem_no','oemNo','OEMNO','OEM','OemNo','productCode','ProductCode','stockCode','StokKodu','kod','Code']));
        $ad=trim((string)$al(['ad','name','Name','urunAdi','UrunAdi','productName','ProductName','MalzemeAdi']));
        if($ad===''||$kod==='') return null;
        $uyum=collect([$al(['uyumlu_marka','vehicleBrand','AracMarka']),$al(['uyumlu_model','vehicleModel','AracModel']),$al(['yil_bas','yearStart']),$al(['yil_son','yearEnd'])])->filter(fn($v)=>filled($v)&&$v!=='0')->implode(' ');
        return [
            'oem_no'=>Str::upper(Str::limit($kod,80,'')), 'urun_kodu'=>Str::limit((string)$al(['id','urun_kodu','productId','ProductId','stockCode'],$kod),80,''),
            'barkod'=>filled($b=$al(['barkod','barcode','Barcode','Barkod']))?Str::limit((string)$b,80,''):null,
            'parca_adi'=>Str::limit($ad,255,''), 'marka'=>Str::limit((string)$al(['marka','brand','Brand','Marka']),100,''),
            'kategori'=>Str::limit((string)$al(['kategori','category','Category','Kategori']),255,''), 'uyumluluk'=>Str::limit($uyum,255,''),
            'birim'=>Str::limit((string)$al(['birim','unit','Unit'],'Adet'),20,''), 'para_birimi'=>Str::limit((string)$al(['para_birimi','currency','Currency'],'TRY'),10,''),
            'satis_fiyati'=>max(0,(float)$al(['fiyat','price','Price','satisFiyati','SalePrice'],0)), 'tedarikci_fiyat'=>max(0,(float)$al(['fiyat','price','Price','satisFiyati','SalePrice'],0)),
            'tedarikci_stok'=>max(0,(float)$al(['stok','stock','Stock','quantity','Quantity'],0)), 'kdv_orani'=>max(0,min(100,(float)$al(['kdv','vat','VatRate','KdvOrani'],20))),
        ];
    }

    private function urunListesi(mixed $veri): array
    {
        if(!is_array($veri)) throw new \RuntimeException('Kaynak JSON ürün listesi döndürmedi.');
        if(array_is_list($veri)) return array_values(array_filter($veri,'is_array'));
        foreach(['data','Data','products','Products','urunler','Urunler','items','Items','result','Result'] as $anahtar) if(isset($veri[$anahtar])&&is_array($veri[$anahtar])) return $this->urunListesi($veri[$anahtar]);
        throw new \RuntimeException('Yanıtta desteklenen ürün listesi alanı bulunamadı.');
    }

    private function benzersizAlanlariHazirla(array $urun, object $kaynak, ?object $mevcut): array
    {
        $urunKodu = trim((string) ($urun['urun_kodu'] ?? ''));
        $urun['urun_kodu'] = $urunKodu === ''
            ? null
            : Str::limit('K'.$kaynak->id.'-'.$urunKodu, 80, '');

        foreach (['urun_kodu', 'barkod'] as $alan) {
            if (! filled($urun[$alan] ?? null)) {
                $urun[$alan] = $mevcut?->{$alan} ?? null;
                continue;
            }

            $cakisan = DB::table('stok_parcalar')
                ->where('firma_id', $kaynak->firma_id)
                ->where($alan, $urun[$alan])
                ->when($mevcut, fn ($sorgu) => $sorgu->where('id', '!=', $mevcut->id))
                ->exists();

            if ($cakisan) {
                $urun[$alan] = $mevcut?->{$alan} ?? null;
            }
        }

        return $urun;
    }

    private function istek(object $kayit)
    {
        $adres=Crypt::decryptString($kayit->adres_sifreli); $this->guvenliAdres($adres);
        return Http::acceptJson()->timeout(120)->retry(2,1500)->get($adres)->throw();
    }

    private function guvenliAdres(string $adres): void
    {
        $parca=parse_url($adres); abort_unless(in_array($parca['scheme']??'', ['http','https'],true)&&filled($parca['host']??null),422,'Geçerli bir HTTP/HTTPS kaynak adresi girin.');
        $host=$parca['host']; $ipler=filter_var($host,FILTER_VALIDATE_IP)?[$host]:(gethostbynamel($host)?:[]);
        abort_if(empty($ipler),422,'Kaynak alan adı çözümlenemedi.');
        foreach($ipler as $ip) abort_if(!filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE),422,'Yerel veya özel ağ adresleri ürün kaynağı olarak kullanılamaz.');
    }

    private function kaynak(Request $request,int $id): object { $firmaId=$this->firmaId($request); return DB::table('stok_urun_kaynaklari')->where('id',$id)->where('firma_id',$firmaId)->firstOrFail(); }
    private function firmaId(Request $r): int { abort_unless(auth()->check()&&(auth()->user()->tamSistemYetkisiVarMi()||auth()->user()->isAdmin()||auth()->user()->isYedekParca()),403);$id=auth()->user()->tamSistemYetkisiVarMi()?($r->integer('firma_id')?:session('aktif_firma_id')?:Firma::where('aktif',true)->value('id')):auth()->user()->firmaPersoneli?->firma_id;abort_unless($id&&Firma::whereKey($id)->where('aktif',true)->exists(),403);return(int)$id; }
    private function hataKaydet(int $id,Throwable $e): void { DB::table('stok_urun_kaynaklari')->where('id',$id)->update(['son_durum'=>'hata','son_hata'=>Str::limit($e->getMessage(),1000),'updated_at'=>now()]); }
}
