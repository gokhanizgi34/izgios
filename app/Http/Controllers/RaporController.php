<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RaporController extends Controller
{
    public function index()
    {
        abort_unless(auth()->check(), 403);
        $user = auth()->user();
        $firmaId = $user->tamSistemYetkisiVarMi() ? null : $user->firmaPersoneli?->firma_id;
        $firmaAdi = $firmaId ? Firma::find($firmaId)?->gosterim_adi : 'Tüm firmalar';
        $adet = fn (string $tablo) => !Schema::hasTable($tablo) ? 0 : DB::table($tablo)->when($firmaId && Schema::hasColumn($tablo, 'firma_id'), fn ($q) => $q->where('firma_id', $firmaId))->count();
        $kartlar = [
            ['Müşteriler', $adet('musteris'), 'bi-people'], ['Araçlar', $adet('araclar'), 'bi-car-front'],
            ['Servis Kayıtları', $adet('servisler'), 'bi-wrench-adjustable'], ['Cari Hesaplar', $adet('cari_hesaplar'), 'bi-wallet2'],
            ['Muhasebe Fişleri', $adet('muhasebe_fisleri'), 'bi-receipt'], ['OEM Parça Kartı', $adet('stok_parcalar'), 'bi-box-seam'],
        ];
        $firmalar = $user->tamSistemYetkisiVarMi() ? Firma::where('aktif', true)->orderBy('unvan')->get(['id', 'unvan']) : collect();
        return view('raporlar.index', compact('kartlar', 'firmaAdi', 'firmalar'));
    }

    public function al(Request $request)
    {
        abort_unless(auth()->check(), 403);
        $user = auth()->user();
        $turler = ['finans','servis','cari','ik','stok','stok_detay','satis_alis','teklifler','alti_aylik_satis','kdv','masraflar','gelir_gider','stok_hareketleri','depo_durumu','hareketsiz_urun','musteri_listesi'];
        $v = $request->validate(['tur' => ['required', 'in:'.implode(',',$turler)], 'donem' => ['required', 'date'], 'firma_id' => ['nullable', 'integer', 'exists:firmas,id']]);
        $izin = ['muhasebe'=>['finans','cari','satis_alis','teklifler','alti_aylik_satis','kdv','masraflar','gelir_gider','musteri_listesi'], 'ik'=>['ik'], 'yedek_parca'=>['stok','stok_detay','stok_hareketleri','depo_durumu','hareketsiz_urun'], 'ofis'=>['servis','cari','satis_alis','teklifler','alti_aylik_satis','musteri_listesi'], 'usta'=>['servis'], 'admin'=>$turler, 'sistem_yoneticisi'=>$turler];
        abort_unless(in_array($v['tur'], $izin[$user->role] ?? [], true), 403, 'Bu rapor türü rolünüze açık değil.');
        $firmaId = $user->tamSistemYetkisiVarMi() ? ($v['firma_id'] ?? null) : $user->firmaPersoneli?->firma_id;
        $donem = Carbon::parse($v['donem'])->startOfMonth(); $bitis = $donem->copy()->endOfMonth();
        $scope = fn ($q, ?string $tablo = null) => $q->when($firmaId, fn ($q) => $q->where(($tablo ? $tablo.'.' : '').'firma_id', $firmaId));
        $rapor = match ($v['tur']) {
            'finans' => $this->finans($scope, $donem, $bitis), 'servis' => $this->servis($scope, $donem, $bitis),
            'cari' => $this->cari($scope, $donem, $bitis), 'ik' => $this->ik($scope, $donem),
            'stok','stok_detay' => $this->stok($scope, $v['tur'] === 'stok_detay'),
            default => $this->ozelRapor($v['tur'],$scope,$donem,$bitis,$firmaId),
        };
        $baslik = ['finans'=>'Finansal Raporlar','servis'=>'Basit Satış / Servis Raporu','cari'=>'Hesap Bakiyeleri','ik'=>'Çalışanlar Raporu','stok'=>'Stok Raporları','stok_detay'=>'Ürünler ve OEM Listesi','satis_alis'=>'Satışlar - Alışlar','teklifler'=>'Teklifler Raporu','alti_aylik_satis'=>'6 Aylık Satışlar','kdv'=>'KDV Raporu','masraflar'=>'Masraflar Raporu','gelir_gider'=>'Gelir Gider Durumu','stok_hareketleri'=>'Stok Hareketleri','depo_durumu'=>'Depo Durumu','hareketsiz_urun'=>'Hareket Görmeyen Ürünler','musteri_listesi'=>'Müşteri Listesi'][$v['tur']];
        return view('raporlar.sonuc', ['baslik'=>$baslik, 'donem'=>$donem, 'tur'=>$v['tur'], 'rapor'=>$rapor, 'firmaAdi'=>$firmaId ? Firma::find($firmaId)?->gosterim_adi : 'Tüm firmalar']);
    }

    private function finans($scope, Carbon $b, Carbon $s): array
    {
        $sum = fn ($yon, $bas, $son) => (float)$scope(DB::table('muhasebe_fisleri'))->where('yon',$yon)->whereBetween('fis_tarihi',[$bas,$son])->sum('tutar');
        $gelir=$sum('gelir',$b,$s); $gider=$sum('gider',$b,$s); $onceki=$b->copy()->subMonth();
        $kayitlar=$scope(DB::table('muhasebe_fisleri'), 'muhasebe_fisleri')->leftJoin('cari_hesaplar','cari_hesaplar.id','=','muhasebe_fisleri.cari_hesap_id')->whereBetween('fis_tarihi',[$b,$s])->orderByDesc('fis_tarihi')->get(['muhasebe_fisleri.fis_no','muhasebe_fisleri.fis_tarihi','muhasebe_fisleri.yon','muhasebe_fisleri.aciklama','muhasebe_fisleri.tutar','cari_hesaplar.unvan as cari']);
        $grafik=collect(range(1,$s->day))->map(fn($g)=>['etiket'=>str_pad($g,2,'0',STR_PAD_LEFT),'gelir'=>$sum('gelir',$b->copy()->day($g),$b->copy()->day($g)),'gider'=>$sum('gider',$b->copy()->day($g),$b->copy()->day($g))]);
        return ['kpis'=>[['Dönem geliri',$gelir,'para','bi-arrow-down-left-circle'],['Dönem gideri',$gider,'para','bi-arrow-up-right-circle'],['Net sonuç',$gelir-$gider,'para','bi-graph-up-arrow'],['Açık cari',$scope(DB::table('cari_hesaplar'))->where('aktif',true)->where('bakiye','!=',0)->count(),'adet','bi-wallet2']], 'grafik'=>$grafik, 'kayitlar'=>$kayitlar, 'tablo'=>['başlık'=>'Dönem mali hareketleri','başlıklar'=>['Fiş No','Tarih','Cari','Açıklama','Yön','Tutar']], 'karsilastirma'=>[['Önceki dönem gelir',$sum('gelir',$onceki,$onceki->copy()->endOfMonth())],['Önceki dönem gider',$sum('gider',$onceki,$onceki->copy()->endOfMonth())]], 'uyarilar'=>[$gider>$gelir?['seviye'=>'kritik','metin'=>'Bu dönemde giderler gelirlerden yüksek. Gider fişleri ve tahsilat planını kontrol edin.']:['seviye'=>'olumlu','metin'=>'Dönem nakit sonucu pozitiftir. Vadesi yaklaşan cari hesapları ayrıca izleyin.']]];
    }

    private function servis($scope, Carbon $b, Carbon $s): array
    {
        $q=$scope(DB::table('servisler'), 'servisler')->whereBetween('servis_tarihi',[$b,$s]); $adet=(clone $q)->count(); $bekleyen=(clone $q)->whereNotIn('durum',['Tamamlandı','Teslim Edildi'])->count();
        $kayitlar=(clone $q)->leftJoin('araclar','araclar.id','=','servisler.arac_id')->leftJoin('musteris','musteris.id','=','servisler.musteri_id')->orderByDesc('servis_tarihi')->get(['servisler.servis_no','servisler.servis_tarihi','servisler.durum','servisler.toplam_tutar','servisler.iscilik_tutari','servisler.parca_tutari','araclar.plaka','musteris.ad_soyad']);
        return ['kpis'=>[['Açılan servis',$adet,'adet','bi-wrench-adjustable'],['Tamamlanan servis',(clone $q)->whereIn('durum',['Tamamlandı','Teslim Edildi'])->count(),'adet','bi-check2-circle'],['Bekleyen iş',$bekleyen,'adet','bi-hourglass-split'],['Servis cirosu',(float)(clone $q)->sum('toplam_tutar'),'para','bi-cash-stack']], 'kayitlar'=>$kayitlar, 'durumlar'=>(clone $q)->selectRaw('durum, COUNT(*) adet')->groupBy('durum')->get(), 'tablo'=>['başlık'=>'Servis işlem listesi','başlıklar'=>['Servis No','Tarih','Plaka','Müşteri','Durum','İşçilik','Parça','Toplam']], 'uyarilar'=>[ $bekleyen?['seviye'=>'uyari','metin'=>"$bekleyen servis kaydı teslim veya tamamlama bekliyor."]:['seviye'=>'olumlu','metin'=>'Seçili dönemde bekleyen servis kaydı bulunmuyor.'] ]];
    }

    private function cari($scope, Carbon $b, Carbon $s): array
    {
        $cariler=$scope(DB::table('cari_hesaplar'))->where('aktif',true)->orderByDesc(DB::raw('ABS(bakiye)'))->get(['unvan','tip','telefon','bakiye']);
        return ['kpis'=>[['Aktif cari',$cariler->count(),'adet','bi-people'],['Toplam alacak',(float)$scope(DB::table('cari_hesaplar'))->where('bakiye','>',0)->sum('bakiye'),'para','bi-arrow-down-left-circle'],['Toplam borç',abs((float)$scope(DB::table('cari_hesaplar'))->where('bakiye','<',0)->sum('bakiye')),'para','bi-arrow-up-right-circle'],['Dönem hareketi',$scope(DB::table('muhasebe_fisleri'))->whereBetween('fis_tarihi',[$b,$s])->count(),'adet','bi-arrow-left-right']], 'cariler'=>$cariler, 'tablo'=>['başlık'=>'Cari hesap bakiyeleri','başlıklar'=>['Cari unvanı','Tip','Telefon','Bakiye']], 'uyarilar'=>[['seviye'=>'uyari','metin'=>'Alacak bakiyesi bulunan cari kartları tahsilat planına göre gözden geçirin.']]];
    }

    private function ik($scope, Carbon $d): array
    {
        $personeller=$scope(DB::table('firma_personels'), 'firma_personels')->join('users','users.id','=','firma_personels.user_id')->leftJoin('ik_personel_ozlukleri',function($j){$j->on('ik_personel_ozlukleri.user_id','=','users.id')->on('ik_personel_ozlukleri.firma_id','=','firma_personels.firma_id');})->where('firma_personels.aktif',true)->select('users.name','users.email','firma_personels.role','ik_personel_ozlukleri.unvan','ik_personel_ozlukleri.net_ucret')->get();
        $bordro=$scope(DB::table('ik_bordrolar'))->whereDate('donem',$d)->get();
        return ['kpis'=>[['Aktif personel',$personeller->count(),'adet','bi-people'],['Dönem bordrosu',$bordro->count(),'adet','bi-receipt'],['Toplam mesai',(float)$bordro->sum('mesai_saati'),'saat','bi-clock-history'],['Ödenecek net',(float)$bordro->sum('odenecek_net'),'para','bi-cash-stack']], 'personeller'=>$personeller, 'tablo'=>['başlık'=>'Aktif personel ve ücret kartları','başlıklar'=>['Personel','E-posta','Sistem rolü','Unvan','Net ücret']], 'uyarilar'=>[$personeller->count()>$bordro->count()?['seviye'=>'uyari','metin'=>'Aktif personellerin bir kısmı için seçili dönem bordrosu henüz oluşturulmamış olabilir.']:['seviye'=>'olumlu','metin'=>'Bordro ve aktif personel kayıtları kontrol edildi.']]];
    }

    private function stok($scope, bool $detay): array
    {
        $parcalar=$scope(DB::table('stok_parcalar'), 'stok_parcalar')->leftJoin('stok_parca_raf_adresleri','stok_parca_raf_adresleri.stok_parca_id','=','stok_parcalar.id')->leftJoin('depo_raflar','depo_raflar.id','=','stok_parca_raf_adresleri.depo_raf_id')->select('stok_parcalar.oem_no','stok_parcalar.parca_adi','stok_parcalar.marka','stok_parcalar.uyumluluk','stok_parcalar.stok_miktari','stok_parcalar.minimum_stok','stok_parcalar.alis_fiyati','stok_parcalar.satis_fiyati','depo_raflar.kod as raf_yeri')->orderBy('stok_parcalar.parca_adi')->get(); $kritik=$parcalar->filter(fn($p)=>$p->stok_miktari<=$p->minimum_stok);
        return ['kpis'=>[['Parça kartı',$parcalar->count(),'adet','bi-box-seam'],['Kritik stok',$kritik->count(),'adet','bi-exclamation-triangle'],['Stok alış değeri',(float)$parcalar->sum(fn($p)=>$p->stok_miktari*$p->alis_fiyati),'para','bi-cash-stack'],['Adreslenmemiş parça',$parcalar->filter(fn($p)=>!$p->raf_yeri)->count(),'adet','bi-geo-alt']], 'parcalar'=>$detay?$parcalar:$kritik->take(20), 'kritik'=>$kritik->take(8), 'tablo'=>['başlık'=>$detay?'OEM, raf ve stok listesi':'Kritik stok listesi','başlıklar'=>['OEM Kodu','Parça adı','Marka','Uyumlu araçlar','Raf yeri','Stok','Kritik eşik','Alış','Satış']], 'uyarilar'=>[$kritik->count()?['seviye'=>'kritik','metin'=>$kritik->count().' parça kritik stok eşiğinde veya altında. Satın alma planı oluşturulmalıdır.']:['seviye'=>'olumlu','metin'=>'Kritik stok eşiğinde parça bulunmuyor.']]];
    }

    private function ozelRapor(string $tur, $scope, Carbon $baslangic, Carbon $bitis, ?int $firmaId): array
    {
        $paraKpi = fn(string $ad, float $deger, string $ikon) => [$ad,$deger,'para',$ikon];
        $adetKpi = fn(string $ad, int $deger, string $ikon) => [$ad,$deger,'adet',$ikon];
        $sonuc = ['kpis'=>[], 'satirlar'=>collect(), 'tablo'=>['başlık'=>'Rapor ayrıntıları','başlıklar'=>[]], 'uyarilar'=>[]];
        if (in_array($tur,['satis_alis','gelir_gider','kdv','masraflar'],true)) {
            $q=$scope(DB::table('muhasebe_fisleri'),'muhasebe_fisleri')->leftJoin('cari_hesaplar','cari_hesaplar.id','=','muhasebe_fisleri.cari_hesap_id')->leftJoin('users','users.id','=','muhasebe_fisleri.olusturan_id')->whereBetween('muhasebe_fisleri.fis_tarihi',[$baslangic,$bitis]);
            if($tur==='masraflar') $q->where('yon','gider');
            $satirlar=(clone $q)->orderByDesc('muhasebe_fisleri.fis_tarihi')->get(['muhasebe_fisleri.*','cari_hesaplar.unvan as cari_unvan','cari_hesaplar.vergi_no','cari_hesaplar.telefon as cari_telefon','users.name as olusturan'])->map(fn($x)=>[$x->fis_no,Carbon::parse($x->fis_tarihi)->format('d.m.Y'),$x->tip,ucfirst($x->yon),$x->cari_unvan ?: '—',$x->vergi_no ?: '—',$x->cari_telefon ?: '—',$x->aciklama ?: '—',ucfirst($x->kaynak),ucfirst($x->durum),$x->olusturan ?: 'Sistem',number_format((float)$x->tutar,2,',','.')]);
            $gelir=(float)(clone $q)->where('yon','gelir')->sum('tutar'); $gider=(float)(clone $q)->where('yon','gider')->sum('tutar');
            $sonuc['kpis']=[$paraKpi('Satış / gelir',$gelir,'bi-arrow-down-left'),$paraKpi('Alış / gider',$gider,'bi-arrow-up-right'),$paraKpi($tur==='kdv'?'Tahmini KDV (%20)':'Net sonuç',$tur==='kdv'?($gelir-$gider)*.20:$gelir-$gider,'bi-calculator'),$adetKpi('Belge / fiş',$satirlar->count(),'bi-receipt')];
            $sonuc['satirlar']=$satirlar;$sonuc['tablo']=['başlık'=>'Ayrıntılı mali hareketler','başlıklar'=>['Belge No','Tarih','Fiş Türü','Yön','Cari','Vergi No','Telefon','Açıklama','Kaynak','Durum','Oluşturan','Tutar']];
        } elseif($tur==='teklifler') {
            $q=$scope(DB::table('teklifler'))->whereBetween('tarih',[$baslangic,$bitis]);$kayitlar=$q->latest('tarih')->get();
            $sonuc['kpis']=[$adetKpi('Teklif',$kayitlar->count(),'bi-file-earmark-text'),$paraKpi('Teklif toplamı',(float)$kayitlar->sum('tutar'),'bi-cash-stack'),$adetKpi('Taslak',$kayitlar->where('durum','taslak')->count(),'bi-pencil'),$adetKpi('Diğer durumlar',$kayitlar->where('durum','!=','taslak')->count(),'bi-check-circle')];
            $sonuc['satirlar']=$kayitlar->map(fn($x)=>[$x->teklif_no,Carbon::parse($x->tarih)->format('d.m.Y'),$x->musteri_unvan,$x->durum,$x->aciklama ?: '—',number_format((float)$x->tutar,2,',','.'),Carbon::parse($x->created_at)->format('d.m.Y H:i')]);$sonuc['tablo']=['başlık'=>'Ayrıntılı teklif listesi','başlıklar'=>['Teklif No','Tarih','Müşteri','Durum','Açıklama','Tutar','Oluşturulma']];
        } elseif($tur==='alti_aylik_satis') {
            $ilk=$bitis->copy()->subMonths(5)->startOfMonth();$kayitlar=collect(range(0,5))->map(function($i)use($scope,$ilk){$ay=$ilk->copy()->addMonths($i);$tutar=(float)$scope(DB::table('servisler'))->whereBetween('servis_tarihi',[$ay->copy()->startOfMonth(),$ay->copy()->endOfMonth()])->sum('toplam_tutar');return [$ay->translatedFormat('F Y'),number_format($tutar,2,',','.'),$tutar];});
            $sonuc['kpis']=[$paraKpi('6 aylık ciro',(float)$kayitlar->sum(2),'bi-graph-up'),$paraKpi('Aylık ortalama',(float)$kayitlar->avg(2),'bi-bar-chart'),$paraKpi('En yüksek ay',(float)$kayitlar->max(2),'bi-trophy'),$adetKpi('Dönem',6,'bi-calendar3')];$sonuc['satirlar']=$kayitlar->map(fn($x)=>[$x[0],$x[1]]);$sonuc['tablo']=['başlık'=>'Aylık satış cirosu','başlıklar'=>['Ay','Servis cirosu']];
        } elseif(in_array($tur,['stok_hareketleri','depo_durumu','hareketsiz_urun'],true)) {
            $q=$scope(DB::table('stok_parcalar'),'stok_parcalar');
            if($tur==='stok_hareketleri'){$kayitlar=$q->join('stok_hareketleri','stok_hareketleri.stok_parca_id','=','stok_parcalar.id')->leftJoin('depolar','depolar.id','=','stok_hareketleri.depo_id')->leftJoin('depo_raflar','depo_raflar.id','=','stok_hareketleri.depo_raf_id')->leftJoin('cari_hesaplar','cari_hesaplar.id','=','stok_hareketleri.cari_hesap_id')->leftJoin('users','users.id','=','stok_hareketleri.olusturan_id')->whereBetween('stok_hareketleri.created_at',[$baslangic,$bitis])->orderByDesc('stok_hareketleri.created_at')->get(['stok_parcalar.oem_no','stok_parcalar.barkod','stok_parcalar.parca_adi','stok_parcalar.birim','stok_hareketleri.yon','stok_hareketleri.miktar','stok_hareketleri.birim_alis_fiyati','stok_hareketleri.toplam_tutar','stok_hareketleri.referans','stok_hareketleri.aciklama','stok_hareketleri.created_at','depolar.ad as depo','depo_raflar.kod as raf','cari_hesaplar.unvan as cari','users.name as olusturan']);$satirlar=$kayitlar->map(fn($x)=>[$x->oem_no,$x->barkod ?: '—',$x->parca_adi,ucfirst($x->yon),$x->miktar.' '.$x->birim,number_format((float)$x->birim_alis_fiyati,2,',','.'),number_format((float)$x->toplam_tutar,2,',','.'),$x->depo ?: '—',$x->raf ?: '—',$x->cari ?: '—',$x->referans ?: '—',$x->aciklama ?: '—',$x->olusturan ?: 'Sistem',Carbon::parse($x->created_at)->format('d.m.Y H:i')]);$basliklar=['OEM','Barkod','Parça','Yön','Miktar','Birim Alış','Toplam','Depo','Raf','Cari','Referans','Açıklama','Oluşturan','Tarih'];}
            else {$q->leftJoin('stok_hareketleri',function($j)use($baslangic,$bitis){$j->on('stok_hareketleri.stok_parca_id','=','stok_parcalar.id')->whereBetween('stok_hareketleri.created_at',[$baslangic,$bitis]);})->selectRaw('stok_parcalar.oem_no,stok_parcalar.parca_adi,stok_parcalar.stok_miktari,stok_parcalar.minimum_stok,COUNT(stok_hareketleri.id) hareket')->groupBy('stok_parcalar.id','stok_parcalar.oem_no','stok_parcalar.parca_adi','stok_parcalar.stok_miktari','stok_parcalar.minimum_stok');if($tur==='hareketsiz_urun')$q->having('hareket','=',0);$kayitlar=$q->get();$satirlar=$kayitlar->map(fn($x)=>[$x->oem_no,$x->parca_adi,$x->stok_miktari,$x->minimum_stok,$x->hareket]);$basliklar=['OEM','Parça','Stok','Kritik eşik','Dönem hareketi'];}
            $sonuc['kpis']=[$adetKpi('Kayıt',$kayitlar->count(),'bi-box-seam'),$adetKpi('Kritik',$kayitlar->filter(fn($x)=>isset($x->stok_miktari)&&$x->stok_miktari<=$x->minimum_stok)->count(),'bi-exclamation-triangle'),$adetKpi('Hareketli',$kayitlar->filter(fn($x)=>(int)($x->hareket??1)>0)->count(),'bi-arrow-left-right'),$adetKpi('Hareketsiz',$kayitlar->filter(fn($x)=>(int)($x->hareket??1)===0)->count(),'bi-pause-circle')];$sonuc['satirlar']=$satirlar;$sonuc['tablo']=['başlık'=>$tur==='stok_hareketleri'?'Stok hareketleri':($tur==='depo_durumu'?'Depo ve stok durumu':'Hareket görmeyen ürünler'),'başlıklar'=>$basliklar];
        } else {
            $q=$scope(DB::table('musteris'))->orderBy('ad_soyad');$kayitlar=$q->get();$sonuc['kpis']=[$adetKpi('Toplam müşteri',$kayitlar->count(),'bi-people'),$adetKpi('Telefon kayıtlı',$kayitlar->whereNotNull('telefon')->count(),'bi-telephone'),$adetKpi('E-posta kayıtlı',$kayitlar->whereNotNull('email')->count(),'bi-envelope'),$adetKpi('Bu ay eklenen',$kayitlar->filter(fn($x)=>Carbon::parse($x->created_at)->between($baslangic,$bitis))->count(),'bi-person-plus')];$sonuc['satirlar']=$kayitlar->map(fn($x)=>[$x->ad_soyad,$x->telefon ?: '—',$x->telefon2 ?: '—',$x->email ?: '—',$x->tc_kimlik_no ?: '—',$x->dogum_tarihi ? Carbon::parse($x->dogum_tarihi)->format('d.m.Y') : '—',$x->adres ?: '—',$x->notlar ?: '—',$x->aktif ? 'Aktif' : 'Pasif',Carbon::parse($x->created_at)->format('d.m.Y H:i')]);$sonuc['tablo']=['başlık'=>'Ayrıntılı müşteri listesi','başlıklar'=>['İsim / Ünvan','Telefon','İkinci Telefon','E-posta','TCKN','Doğum Tarihi','Adres','Notlar','Durum','Kayıt Tarihi']];
        }
        $sonuc['uyarilar'][]=['seviye'=>$sonuc['satirlar']->isEmpty()?'uyari':'olumlu','metin'=>$sonuc['satirlar']->isEmpty()?'Seçilen kriterlerde kayıt bulunamadı.':'Rapor gerçek İZGİOS kayıtlarından hazırlandı.']; return $sonuc;
    }
}
