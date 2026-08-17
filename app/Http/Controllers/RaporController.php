<?php

namespace App\Http\Controllers;

use App\Models\Firma;
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
        $alan = match ($user->role) { 'muhasebe' => 'muhasebe', 'yedek_parca' => 'depo', 'ik' => 'ik', 'ofis' => 'ofis', 'usta' => 'usta', default => 'yonetim' };
        $adet = function (string $tablo, ?string $kolon = 'firma_id') use ($firmaId) {
            if ($firmaId && (!$kolon || !Schema::hasColumn($tablo, $kolon))) return 0;
            return DB::table($tablo)->when($firmaId && $kolon, fn ($q) => $q->where($kolon, $firmaId))->count();
        };
        $buAyFirmaKaydi = function (string $tablo) use ($firmaId) {
            if ($firmaId && !Schema::hasColumn($tablo, 'firma_id')) return 0;
            return DB::table($tablo)->when($firmaId, fn ($q) => $q->where('firma_id', $firmaId))->whereMonth('created_at', now()->month)->count();
        };
        $kartlar = match ($alan) {
            'muhasebe' => [['Cari Hesaplar', $adet('cari_hesaplar'), 'bi-wallet2'], ['Muhasebe Fişleri', $adet('muhasebe_fisleri'), 'bi-receipt'], ['Kesilen Faturalar', $adet('faturalar'), 'bi-file-earmark-text'], ['Teklifler', $adet('teklifler'), 'bi-file-earmark-check']],
            'depo' => [['OEM Parça Kartı', $adet('stok_parcalar'), 'bi-box-seam'], ['Kritik Stok', DB::table('stok_parcalar')->when($firmaId, fn($q)=>$q->where('firma_id',$firmaId))->whereColumn('stok_miktari','<=','minimum_stok')->count(), 'bi-exclamation-triangle'], ['Stok Hareketi', $adet('stok_hareketleri', null), 'bi-arrow-left-right'], ['Kontrol Bekleyen OEM', DB::table('stok_parcalar')->when($firmaId, fn($q)=>$q->where('firma_id',$firmaId))->where('oem_durum','kontrol_bekliyor')->count(), 'bi-hourglass-split']],
            'ik' => [['Aktif Personel', DB::table('firma_personels')->when($firmaId, fn($q)=>$q->where('firma_id',$firmaId))->where('aktif',true)->count(), 'bi-people'], ['Özlük Kaydı', $adet('ik_personel_ozlukleri'), 'bi-person-vcard'], ['Bordro Kaydı', $adet('ik_bordrolar'), 'bi-cash-stack'], ['Şifre Talebi', $adet('sifre_yenileme_talepleri'), 'bi-key']],
            'ofis' => [['Müşteriler', $adet('musteris'), 'bi-people'], ['Araçlar', $adet('araclar'), 'bi-car-front'], ['Bu Ay Eklenen Müşteri', $buAyFirmaKaydi('musteris'), 'bi-person-plus'], ['Bu Ay Eklenen Araç', $buAyFirmaKaydi('araclar'), 'bi-car-front-fill']],
            'usta' => [['Kendi İşlerim', 0, 'bi-tools'], ['Tamamlanan İşlem', 0, 'bi-check2-circle'], ['Bekleyen İş', 0, 'bi-hourglass-split'], ['Not', 'Atama verisi bekleniyor', 'bi-info-circle']],
            default => [['Firmalar', $adet('firmas', null), 'bi-buildings'], ['Müşteriler', $adet('musteris'), 'bi-people'], ['Araçlar', $adet('araclar'), 'bi-car-front'], ['Servis Kayıtları', $adet('servisler'), 'bi-wrench-adjustable'], ['Personel', DB::table('firma_personels')->when($firmaId, fn($q)=>$q->where('firma_id',$firmaId))->where('aktif',true)->count(), 'bi-person-badge'], ['OEM Parça', $adet('stok_parcalar'), 'bi-box-seam']],
        };
        return view('raporlar.index', compact('kartlar', 'firmaAdi', 'alan'));
    }

    public function al(\Illuminate\Http\Request $request)
    {
        abort_unless(auth()->check(), 403);
        $v=$request->validate(['tur'=>['required','in:finans,ik,stok'],'donem'=>['required','date']]);$u=auth()->user();
        $izin=['muhasebe'=>['finans'],'ik'=>['ik'],'yedek_parca'=>['stok'],'admin'=>['finans','ik','stok'],'sistem_yoneticisi'=>['finans','ik','stok']];
        abort_unless(in_array($v['tur'],$izin[$u->role]??[],true),403,'Bu rapor türü rolünüze açık değil.');
        $firmaId=$u->tamSistemYetkisiVarMi()?null:$u->firmaPersoneli?->firma_id;$d=\Carbon\Carbon::parse($v['donem'])->startOfMonth();$b=$d->copy()->endOfMonth();
        $scope=fn($q)=>$q->when($firmaId,fn($q)=>$q->where('firma_id',$firmaId));
        $satirlar=match($v['tur']){
            'finans'=>[['Gelir',$scope(DB::table('muhasebe_fisleri'))->where('yon','gelir')->whereBetween('fis_tarihi',[$d,$b])->sum('tutar')],['Gider',$scope(DB::table('muhasebe_fisleri'))->where('yon','gider')->whereBetween('fis_tarihi',[$d,$b])->sum('tutar')],['Açık cari hesap',$scope(DB::table('cari_hesaplar'))->where('aktif',true)->count()]],
            'ik'=>[['Aktif personel',$scope(DB::table('firma_personels'))->where('aktif',true)->count()],['Bordro',$scope(DB::table('ik_bordrolar'))->whereDate('donem',$d)->count()],['Mesai saati',$scope(DB::table('ik_bordrolar'))->whereDate('donem',$d)->sum('mesai_saati')]],
            'stok'=>[['Parça kartı',$scope(DB::table('stok_parcalar'))->count()],['Kritik stok',$scope(DB::table('stok_parcalar'))->whereColumn('stok_miktari','<=','minimum_stok')->count()],['Stok maliyeti',$scope(DB::table('stok_parcalar'))->selectRaw('COALESCE(SUM(stok_miktari*alis_fiyati),0) toplam')->value('toplam')]],
        };return view('raporlar.sonuc',['baslik'=>['finans'=>'Finans Raporu','ik'=>'İK Raporu','stok'=>'Stok Raporu'][$v['tur']],'satirlar'=>$satirlar,'donem'=>$d]);
    }
}
