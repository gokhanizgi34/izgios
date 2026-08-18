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
        $v = $request->validate(['tur' => ['required', 'in:finans,servis,cari,ik,stok,stok_detay'], 'donem' => ['required', 'date'], 'firma_id' => ['nullable', 'integer', 'exists:firmas,id']]);
        $izin = ['muhasebe'=>['finans','cari'], 'ik'=>['ik'], 'yedek_parca'=>['stok','stok_detay'], 'ofis'=>['servis','cari'], 'usta'=>['servis'], 'admin'=>['finans','servis','cari','ik','stok','stok_detay'], 'sistem_yoneticisi'=>['finans','servis','cari','ik','stok','stok_detay']];
        abort_unless(in_array($v['tur'], $izin[$user->role] ?? [], true), 403, 'Bu rapor türü rolünüze açık değil.');
        $firmaId = $user->tamSistemYetkisiVarMi() ? ($v['firma_id'] ?? null) : $user->firmaPersoneli?->firma_id;
        $donem = Carbon::parse($v['donem'])->startOfMonth(); $bitis = $donem->copy()->endOfMonth();
        $scope = fn ($q, ?string $tablo = null) => $q->when($firmaId, fn ($q) => $q->where(($tablo ? $tablo.'.' : '').'firma_id', $firmaId));
        $rapor = match ($v['tur']) {
            'finans' => $this->finans($scope, $donem, $bitis), 'servis' => $this->servis($scope, $donem, $bitis),
            'cari' => $this->cari($scope, $donem, $bitis), 'ik' => $this->ik($scope, $donem),
            default => $this->stok($scope, $v['tur'] === 'stok_detay'),
        };
        $baslik = ['finans'=>'Finans ve Muhasebe Raporu','servis'=>'Servis Operasyon Raporu','cari'=>'Cari Hesap ve Tahsilat Raporu','ik'=>'İK ve Personel Raporu','stok'=>'Yedek Parça ve Stok Raporu','stok_detay'=>'OEM ve Raf Bazlı Stok Listesi'][$v['tur']];
        return view('raporlar.sonuc', ['baslik'=>$baslik, 'donem'=>$donem, 'tur'=>$v['tur'], 'rapor'=>$rapor, 'firmaAdi'=>$firmaId ? Firma::find($firmaId)?->gosterim_adi : 'Tüm firmalar']);
    }

    private function finans($scope, Carbon $b, Carbon $s): array
    {
        $sum = fn ($yon, $bas, $son) => (float)$scope(DB::table('muhasebe_fisleri'))->where('yon',$yon)->whereBetween('fis_tarihi',[$bas,$son])->sum('tutar');
        $gelir=$sum('gelir',$b,$s); $gider=$sum('gider',$b,$s); $onceki=$b->copy()->subMonth();
        $kayitlar=$scope(DB::table('muhasebe_fisleri'), 'muhasebe_fisleri')->leftJoin('cari_hesaplar','cari_hesaplar.id','=','muhasebe_fisleri.cari_hesap_id')->whereBetween('fis_tarihi',[$b,$s])->orderByDesc('fis_tarihi')->limit(50)->get(['muhasebe_fisleri.fis_no','muhasebe_fisleri.fis_tarihi','muhasebe_fisleri.yon','muhasebe_fisleri.aciklama','muhasebe_fisleri.tutar','cari_hesaplar.unvan as cari']);
        $grafik=collect(range(1,$s->day))->map(fn($g)=>['etiket'=>str_pad($g,2,'0',STR_PAD_LEFT),'gelir'=>$sum('gelir',$b->copy()->day($g),$b->copy()->day($g)),'gider'=>$sum('gider',$b->copy()->day($g),$b->copy()->day($g))]);
        return ['kpis'=>[['Dönem geliri',$gelir,'para','bi-arrow-down-left-circle'],['Dönem gideri',$gider,'para','bi-arrow-up-right-circle'],['Net sonuç',$gelir-$gider,'para','bi-graph-up-arrow'],['Açık cari',$scope(DB::table('cari_hesaplar'))->where('aktif',true)->where('bakiye','!=',0)->count(),'adet','bi-wallet2']], 'grafik'=>$grafik, 'kayitlar'=>$kayitlar, 'tablo'=>['başlık'=>'Dönem mali hareketleri','başlıklar'=>['Fiş No','Tarih','Cari','Açıklama','Yön','Tutar']], 'karsilastirma'=>[['Önceki dönem gelir',$sum('gelir',$onceki,$onceki->copy()->endOfMonth())],['Önceki dönem gider',$sum('gider',$onceki,$onceki->copy()->endOfMonth())]], 'uyarilar'=>[$gider>$gelir?['seviye'=>'kritik','metin'=>'Bu dönemde giderler gelirlerden yüksek. Gider fişleri ve tahsilat planını kontrol edin.']:['seviye'=>'olumlu','metin'=>'Dönem nakit sonucu pozitiftir. Vadesi yaklaşan cari hesapları ayrıca izleyin.']]];
    }

    private function servis($scope, Carbon $b, Carbon $s): array
    {
        $q=$scope(DB::table('servisler'), 'servisler')->whereBetween('servis_tarihi',[$b,$s]); $adet=(clone $q)->count(); $bekleyen=(clone $q)->whereNotIn('durum',['Tamamlandı','Teslim Edildi'])->count();
        $kayitlar=(clone $q)->leftJoin('araclar','araclar.id','=','servisler.arac_id')->leftJoin('musteris','musteris.id','=','servisler.musteri_id')->orderByDesc('servis_tarihi')->limit(50)->get(['servisler.servis_no','servisler.servis_tarihi','servisler.durum','servisler.toplam_tutar','servisler.iscilik_tutari','servisler.parca_tutari','araclar.plaka','musteris.ad_soyad']);
        return ['kpis'=>[['Açılan servis',$adet,'adet','bi-wrench-adjustable'],['Tamamlanan servis',(clone $q)->whereIn('durum',['Tamamlandı','Teslim Edildi'])->count(),'adet','bi-check2-circle'],['Bekleyen iş',$bekleyen,'adet','bi-hourglass-split'],['Servis cirosu',(float)(clone $q)->sum('toplam_tutar'),'para','bi-cash-stack']], 'kayitlar'=>$kayitlar, 'durumlar'=>(clone $q)->selectRaw('durum, COUNT(*) adet')->groupBy('durum')->get(), 'tablo'=>['başlık'=>'Servis işlem listesi','başlıklar'=>['Servis No','Tarih','Plaka','Müşteri','Durum','İşçilik','Parça','Toplam']], 'uyarilar'=>[ $bekleyen?['seviye'=>'uyari','metin'=>"$bekleyen servis kaydı teslim veya tamamlama bekliyor."]:['seviye'=>'olumlu','metin'=>'Seçili dönemde bekleyen servis kaydı bulunmuyor.'] ]];
    }

    private function cari($scope, Carbon $b, Carbon $s): array
    {
        $cariler=$scope(DB::table('cari_hesaplar'))->where('aktif',true)->orderByDesc(DB::raw('ABS(bakiye)'))->limit(50)->get(['unvan','tip','telefon','bakiye']);
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
}
