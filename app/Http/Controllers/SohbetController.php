<?php

namespace App\Http\Controllers;

use App\Models\SohbetMesaji;
use App\Models\SohbetOdasi;
use App\Models\Firma;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SohbetController extends Controller
{
    public function index(Request $request)
    {
        $firmaId = $this->firmaId($request);
        $firmalar = auth()->user()->tamSistemYetkisiVarMi() ? Firma::query()->orderBy('unvan')->get() : collect();

        if (!$firmaId) {
            return view('sohbet.stable-index-v2', ['oda' => null, 'mesajlar' => collect(), 'firmalar' => $firmalar, 'firmaId' => null, 'personelSayisi' => 0, 'etiketAdlari' => collect()]);
        }

        $oda = SohbetOdasi::firstOrCreate(
            ['firma_id' => $firmaId, 'tip' => 'genel'],
            ['ad' => 'Genel Sohbet', 'olusturan_id' => auth()->id()]
        );
        $mesajlar = $oda->mesajlar()->with('kullanici')->oldest()->limit(150)->get();
        $personelSayisi = DB::table('firma_personels')->where('firma_id', $firmaId)->where('aktif', true)->count();
        $etiketAdlari = User::query()->where('status', 'aktif')->whereHas('firmaPersoneli', fn ($sorgu) => $sorgu->where('firma_id', $firmaId))->orderBy('name')->get()->map(fn (User $kullanici) => $kullanici->tamAdi());

        return view('sohbet.stable-index-v2', compact('oda', 'mesajlar', 'firmalar', 'firmaId', 'personelSayisi', 'etiketAdlari'));
    }

    public function odaOlustur(Request $request)
    {
        abort(404);
    }

    public function mesajGonder(Request $request, SohbetOdasi $oda)
    {
        $firmaId = $this->firmaId($request);
        abort_unless($oda->firma_id === $firmaId, 403);
        abort_unless($oda->tip === 'genel', 404);
        $veri = $request->validate(['mesaj' => ['required', 'string', 'max:4000']]);
        $mesaj = SohbetMesaji::create(['sohbet_odasi_id' => $oda->id, 'user_id' => auth()->id(), 'mesaj' => $veri['mesaj']]);
        $this->etiketlenenlereEpostaGonder($oda, $mesaj);
        return redirect()->route('sohbet.index', ['firma' => $firmaId]);
    }

    public function mesajlarJson(Request $request, SohbetOdasi $oda)
    {
        $firmaId = $this->firmaId($request);
        abort_unless($oda->firma_id === $firmaId && $oda->tip === 'genel', 403);
        $sonId = (int) $request->integer('son_id');

        return response()->json([
            'mesajlar' => $oda->mesajlar()->with('kullanici')->where('id', '>', $sonId)->oldest()->limit(80)->get()->map(fn (SohbetMesaji $mesaj) => [
                'id' => $mesaj->id,
                'user_id' => $mesaj->user_id,
                'ad' => $mesaj->kullanici?->tamAdi() ?? 'Kullanıcı',
                'rol' => $mesaj->kullanici?->rolAdi() ?? 'Firma kullanıcısı',
                'mesaj' => $mesaj->mesaj,
                'tarih' => $mesaj->created_at->format('d.m.Y H:i'),
            ]),
        ]);
    }

    private function etiketlenenlereEpostaGonder(SohbetOdasi $oda, SohbetMesaji $mesaj): void
    {
        $metin = mb_strtolower($mesaj->mesaj, 'UTF-8');
        $kullanicilar = User::query()->whereHas('firmaPersoneli', fn ($sorgu) => $sorgu->where('firma_id', $oda->firma_id))->where('status', 'aktif')->get();
        $etiketlenenler = $kullanicilar->filter(function (User $kullanici) use ($metin, $mesaj) {
            if ($kullanici->id === $mesaj->user_id || blank($kullanici->email)) return false;
            $ad = mb_strtolower($kullanici->tamAdi(), 'UTF-8');
            $emailKullaniciAdi = mb_strtolower(strstr($kullanici->email, '@', true) ?: '', 'UTF-8');
            return str_contains($metin, '@' . $ad) || ($emailKullaniciAdi !== '' && str_contains($metin, '@' . $emailKullaniciAdi));
        });

        if ($etiketlenenler->isEmpty()) return;

        $firma = $oda->firma;
        foreach ($etiketlenenler as $etiketlenen) {
            try {
                Mail::send('emails.sohbet-etiket', [
                    'gonderen' => $mesaj->kullanici?->tamAdi() ?? 'Bir firma kullanıcısı',
                    'firma' => $firma?->unvan ?? 'Firma',
                    'mesaj' => $mesaj->mesaj,
                    'sohbetUrl' => route('sohbet.index', ['firma' => $oda->firma_id]),
                ], fn ($mail) => $mail->to($etiketlenen->email)->subject('İZGİOS sohbetinde sizden bahsedildi'));
            } catch (\Throwable $hata) {
                Log::warning('Sohbet etiket e-postası gönderilemedi.', ['mesaj_id' => $mesaj->id, 'kullanici_id' => $etiketlenen->id, 'sebep' => $hata->getMessage()]);
            }
        }
    }

    private function firmaId(Request $request): ?int
    {
        abort_unless(auth()->check(), 403);
        $firmaId = session('aktif_firma_id') ?: auth()->user()->firmaPersoneli?->firma_id;
        if (!$firmaId && auth()->user()->tamSistemYetkisiVarMi()) {
            $firmaId = $request->input('firma_id', $request->query('firma'));
            if (!$firmaId) { return null; }
            abort_unless(Firma::query()->whereKey($firmaId)->exists(), 404);
        }
        abort_unless($firmaId, 403, 'Sohbet için kullanıcıya bağlı aktif firma bulunmalıdır.');
        return (int) $firmaId;
    }
}
