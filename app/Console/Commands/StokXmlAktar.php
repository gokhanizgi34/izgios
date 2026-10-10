<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use XMLReader;

class StokXmlAktar extends Command
{
    protected $signature = 'stok:xml-aktar {dosya} {--firma=* : Aktarım yapılacak firma kimlikleri; boşsa tüm aktif firmalar}';
    protected $description = 'Tedarikçi XML ürün kataloğunu firma stok kartlarına aktarır';

    public function handle(): int
    {
        $dosya = realpath((string) $this->argument('dosya'));
        if (! $dosya || ! is_file($dosya)) {
            $this->error('XML dosyası bulunamadı.');
            return self::FAILURE;
        }

        $firmaSecimi = array_values(array_filter(array_map('intval', $this->option('firma'))));
        $firmalar = DB::table('firmas')->where('aktif', true)
            ->when($firmaSecimi !== [], fn ($q) => $q->whereIn('id', $firmaSecimi))
            ->pluck('unvan', 'id');
        if ($firmalar->isEmpty()) {
            $this->error('Aktif firma bulunamadı.');
            return self::FAILURE;
        }

        foreach ($firmalar as $firmaId => $unvan) {
            $this->info($unvan.' kataloğu aktarılıyor...');
            $this->firmaAktar($dosya, (int) $firmaId);
        }
        return self::SUCCESS;
    }

    private function firmaAktar(string $dosya, int $firmaId): void
    {
        $kaynakId = DB::table('stok_urun_kaynaklari')->insertGetId([
            'firma_id'=>$firmaId, 'ad'=>'XML · CLI · '.now()->format('Ymd-His'),
            'adres_sifreli'=>Crypt::encryptString('xml://'.basename($dosya)), 'aktif'=>false,
            'son_durum'=>'aktariliyor', 'created_at'=>now(), 'updated_at'=>now(),
        ]);
        $okuyucu = new XMLReader();
        if (! $okuyucu->open($dosya, null, LIBXML_NONET | LIBXML_COMPACT | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            throw new RuntimeException('XML dosyası açılamadı.');
        }
        $sayac=['toplam'=>0,'eklenen'=>0,'guncellenen'=>0,'atlanmis'=>0]; $grup=[];
        try {
            while ($okuyucu->read()) {
                if ($okuyucu->nodeType !== XMLReader::ELEMENT || $okuyucu->name !== 'urun') continue;
                $xml=simplexml_load_string($okuyucu->readOuterXml(),'SimpleXMLElement',LIBXML_NONET|LIBXML_NOCDATA);
                if ($xml===false) { $sayac['atlanmis']++; continue; }
                $grup[]=[
                    'urun_kodu'=>trim((string)$xml->id), 'parca_adi'=>trim((string)$xml->ad),
                    'kategori'=>trim((string)$xml->kategori), 'marka'=>trim((string)$xml->marka),
                    'oem_no'=>Str::upper(trim((string)$xml->oem_no)), 'fiyat'=>(float)$xml->fiyat,
                    'stok'=>(float)$xml->stok,
                    'uyumluluk'=>trim(implode(' ',array_filter([(string)$xml->uyumlu_marka,(string)$xml->uyumlu_model,(string)$xml->yil_bas !== '0'?(string)$xml->yil_bas:null,(string)$xml->yil_son !== '0'?(string)$xml->yil_son:null]))),
                ]; $sayac['toplam']++;
                if(count($grup)>=250){$this->grupYaz($grup,$firmaId,$kaynakId,$sayac);$grup=[];}
            }
            if($grup!==[])$this->grupYaz($grup,$firmaId,$kaynakId,$sayac);
            DB::table('stok_urun_kaynaklari')->where('id',$kaynakId)->update(['son_durum'=>'basarili','son_urun_sayisi'=>$sayac['toplam'],'son_senkron_at'=>now(),'updated_at'=>now()]);
            $this->line("{$sayac['eklenen']} yeni, {$sayac['guncellenen']} güncel, {$sayac['atlanmis']} atlanan.");
        } finally { $okuyucu->close(); }
    }

    private function grupYaz(array $urunler,int $firmaId,int $kaynakId,array &$sayac):void
    {
        DB::transaction(function()use($urunler,$firmaId,$kaynakId,&$sayac){foreach($urunler as $urun){
            if($urun['oem_no']===''||$urun['parca_adi']===''){$sayac['atlanmis']++;continue;}
            $mevcut=DB::table('stok_parcalar')->where('firma_id',$firmaId)->where('oem_no',$urun['oem_no'])->first();
            $deger=['urun_kaynak_id'=>$kaynakId,'urun_kodu'=>Str::limit('XML'.$kaynakId.'-'.$urun['urun_kodu'],80,''),'parca_adi'=>Str::limit($urun['parca_adi'],255,''),'marka'=>Str::limit($urun['marka'],100,''),'kategori'=>Str::limit($urun['kategori'],255,''),'uyumluluk'=>Str::limit($urun['uyumluluk'],255,''),'satis_fiyati'=>max(0,$urun['fiyat']),'tedarikci_fiyat'=>max(0,$urun['fiyat']),'tedarikci_stok'=>max(0,$urun['stok']),'kaynak_senkron_at'=>now(),'oem_durum'=>'kaynak_dogrulandi','aktif'=>true,'updated_at'=>now()];
            if($mevcut){DB::table('stok_parcalar')->where('id',$mevcut->id)->update($deger);$sayac['guncellenen']++;}
            else{DB::table('stok_parcalar')->insert(array_merge($deger,['firma_id'=>$firmaId,'oem_no'=>$urun['oem_no'],'stok_miktari'=>0,'minimum_stok'=>0,'alis_fiyati'=>0,'birim'=>'Adet','para_birimi'=>'TRY','kdv_orani'=>20,'created_at'=>now()]));$sayac['eklenen']++;}
        }});
    }
}
