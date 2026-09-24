<?php

namespace App\Http\Controllers;

use App\Models\AracQrKodu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AracQrHavuzuController extends Controller
{
    public function index(Request $request)
    {
        $this->sistemYoneticisiKontrol();
        $partiler = AracQrKodu::query()
            ->select('parti_kodu', DB::raw('COUNT(*) toplam'), DB::raw("SUM(CASE WHEN durum = 'bos' THEN 1 ELSE 0 END) bos"), DB::raw('MAX(created_at) olusturulma_at'))
            ->groupBy('parti_kodu')
            ->orderByDesc('olusturulma_at')
            ->get();

        return view('araclar.qr-havuzu', compact('partiler'));
    }

    public function uret()
    {
        $this->sistemYoneticisiKontrol();
        $parti = (string) Str::uuid();
        $simdi = now();
        $satirlar = [];

        for ($sira = 1; $sira <= 100; $sira++) {
            $satirlar[] = [
                'token' => (string) Str::uuid(),
                'parti_kodu' => $parti,
                'sira_no' => $sira,
                'durum' => 'bos',
                'olusturan_id' => auth()->id(),
                'created_at' => $simdi,
                'updated_at' => $simdi,
            ];
        }

        AracQrKodu::insert($satirlar);

        return redirect()->route('arac-qr-havuzu.index', ['parti' => $parti])
            ->with('success', '100 adet araç QR kodu üretildi. Yeni parti aşağıda vurgulandı; Aç / PDF Kaydet ile baskıya hazırlayabilirsiniz.');
    }

    public function yazdir(string $parti)
    {
        $this->sistemYoneticisiKontrol();
        abort_unless(Str::isUuid($parti), 404);
        $kodlar = AracQrKodu::where('parti_kodu', $parti)->orderBy('sira_no')->get();
        abort_if($kodlar->isEmpty(), 404);

        $etiketler = $kodlar->map(fn (AracQrKodu $kod) => [
            'qr' => QrCode::format('svg')->size(220)->margin(1)->generate(route('araclar.qr.show', $kod->token)),
        ]);

        return view('araclar.qr-havuzu-yazdir', compact('etiketler', 'parti'));
    }

    private function sistemYoneticisiKontrol(): void
    {
        abort_unless(auth()->check() && auth()->user()->tamSistemYetkisiVarMi(), 403);
    }
}
