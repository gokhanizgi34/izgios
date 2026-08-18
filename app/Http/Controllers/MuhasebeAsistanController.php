<?php

namespace App\Http\Controllers;

use App\Services\DervisBilgiBankasiServisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MuhasebeAsistanController extends Controller
{
    public function yanitla(Request $request)
    {
        abort_unless(auth()->check(), 403);
        $veri = $request->validate(['mesaj' => ['required', 'string', 'max:2000']]);
        $mesaj = $this->maskele($veri['mesaj']);
        $bilgiBankasi = app(DervisBilgiBankasiServisi::class);
        $bilgiCevabi = $bilgiBankasi->cevap($bilgiBankasi->eslestir($mesaj));

        if ($bilgiCevabi) {
            return response()->json(['yanit' => $bilgiCevabi['cozum']]);
        }

        if (config('services.izgios_ai.provider') !== 'openai' || blank(config('services.izgios_ai.key'))) {
            return response()->json(['yanit' => 'Bilgi Merkezi’nde bu soru için hazır bir yanıt bulunamadı. Sol menüdeki Sık Sorulan Sorular sayfasını açın; teknik sorunlarda Destek Merkezi’nden ekran adı ve hata koduyla talep oluşturun.']);
        }

        $sssBaglami = $bilgiBankasi->asistanBaglami();

        $istem = <<<TEXT
Sen İZGİOS Genel Asistanısın. Türkçe, kısa ve kurumsal yanıt ver.
İZGİOS oto servis otomasyonundaki müşteri, araç, servis kabul, iş emri, depo, muhasebe, rapor, kullanıcı, firma, şube, QR, bildirim ve ayarlar ekranları hakkında yol göster.
Asla işlem yapma, veri silme, belge oluşturma, dış sisteme gönderme veya muhasebe/hukuk hükmü verme. İşlem gerektiğinde kullanıcıya hangi menüye gideceğini ve hangi alanları kontrol edeceğini söyle.
Çözümsüz kalan teknik hata, veri uyuşmazlığı veya yetki sorununda kullanıcıdan hata kodu ve ekran adresini isteyip Destek Merkezi'ne yönlendir.
Kişisel veri, API anahtarı veya gizli bilgiyi isteme. Yanıt en fazla 6 kısa maddeden oluşsun.

Bilgi Merkezi cevapları (uygun olduğunda bunları doğrudan temel al):
{$sssBaglami}

Kullanıcı mesajı: {$mesaj}
TEXT;

        try {
            $cevap = Http::acceptJson()->withToken(config('services.izgios_ai.key'))->timeout(30)->post('https://api.openai.com/v1/responses', [
                'model' => config('services.izgios_ai.model', 'gpt-5.6'),
                'input' => $istem,
            ]);
            if (! $cevap->successful()) throw new \RuntimeException('AI HTTP '.$cevap->status());
            $metin = data_get($cevap->json(), 'output.0.content.0.text') ?? data_get($cevap->json(), 'output.0.content.0.value') ?? data_get($cevap->json(), 'output_text');
            if (! filled($metin)) {
                $metin = collect(data_get($cevap->json(), 'output', []))->flatMap(fn ($cikti) => data_get($cikti, 'content', []))->map(fn ($icerik) => data_get($icerik, 'text') ?? data_get($icerik, 'value') ?? '')->filter()->implode("\n");
            }
            if (! filled($metin)) throw new \RuntimeException('AI yanıt metni boş');
            return response()->json(['yanit' => mb_strimwidth(trim($metin), 0, 2200, '…', 'UTF-8')]);
        } catch (\Throwable $hata) {
            Log::warning('Muhasebe asistanı yanıt hatası.', ['sebep' => $hata->getMessage(), 'kullanici_id' => auth()->id()]);
            return response()->json(['yanit' => "Şu an yapay zekâ yanıtına ulaşılamadı. Sorununuzu çözebilmemiz için Destek Merkezi'nden talep açın; başlıkta ekran adını, açıklamada yaptığınız işlemi ve varsa hata kodunu yazın. Sistem Yöneticisi kuyruğuna yönlendirilir."], 200);
        }
    }

    private function maskele(string $metin): string
    {
        $metin = preg_replace('/[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}/u', '[e-posta]', $metin);
        return preg_replace('/\b\d{10,11}\b/u', '[telefon]', $metin);
    }
}
