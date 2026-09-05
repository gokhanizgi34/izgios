<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class YapayZekaIstemcisi
{
    public function hazirMi(): bool
    {
        return in_array(config('services.izgios_ai.provider'), ['openai', 'gemini'], true)
            && filled(config('services.izgios_ai.key'));
    }

    public function metin(string $istem): string
    {
        $yanit = $this->istek([['text' => $istem]]);
        return $this->metniAl($yanit);
    }

    public function gorselJson(string $istem, string $mime, string $veri, array $sema): array
    {
        if (config('services.izgios_ai.provider') === 'gemini') {
            $yanit = $this->istek([
                ['text' => $istem],
                ['inline_data' => ['mime_type' => $mime, 'data' => base64_encode($veri)]],
            ], ['responseMimeType' => 'application/json', 'responseJsonSchema' => $sema, 'maxOutputTokens' => 1200]);
        } else {
            $yanit = Http::acceptJson()->withToken(config('services.izgios_ai.key'))->timeout(60)->retry(1, 800)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.izgios_ai.model', 'gpt-5.6'), 'store' => false,
                    'input' => [['role'=>'user','content'=>[
                        ['type'=>'input_text','text'=>$istem],
                        ['type'=>'input_image','detail'=>'high','image_url'=>'data:'.$mime.';base64,'.base64_encode($veri)],
                    ]]],
                    'text' => ['format'=>['type'=>'json_schema','name'=>'belge_okuma','strict'=>true,'schema'=>$sema]],
                    'max_output_tokens' => 1200,
                ]);
        }

        $metin = $this->metniAl($yanit);
        $sonuc = json_decode($metin, true);
        if (! is_array($sonuc)) throw new RuntimeException('Yapay zekâdan geçerli JSON sonucu alınamadı.');
        return $sonuc;
    }

    private function istek(array $parcalar, array $uretimAyarlari = []): Response
    {
        $saglayici = config('services.izgios_ai.provider');
        if (! $this->hazirMi()) throw new RuntimeException('Yapay zekâ bağlantısı yapılandırılmamış.');

        if ($saglayici === 'gemini') {
            $model = rawurlencode(config('services.izgios_ai.model', 'gemini-2.5-flash'));
            return Http::acceptJson()->withHeaders(['x-goog-api-key' => config('services.izgios_ai.key')])->timeout(60)->retry(1, 800)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'contents' => [['role'=>'user','parts'=>$parcalar]],
                    'generationConfig' => $uretimAyarlari,
                ]);
        }

        return Http::acceptJson()->withToken(config('services.izgios_ai.key'))->timeout(60)->retry(1, 800)
            ->post('https://api.openai.com/v1/responses', ['model'=>config('services.izgios_ai.model', 'gpt-5.6'), 'input'=>$parcalar[0]['text'] ?? '']);
    }

    private function metniAl(Response $yanit): string
    {
        if (! $yanit->successful()) throw new RuntimeException('Yapay zekâ HTTP '.$yanit->status());
        $metin = config('services.izgios_ai.provider') === 'gemini'
            ? data_get($yanit->json(), 'candidates.0.content.parts.0.text')
            : (data_get($yanit->json(), 'output.0.content.0.text') ?? data_get($yanit->json(), 'output_text'));
        if (! is_string($metin) || blank($metin)) throw new RuntimeException('Yapay zekâ yanıt metni boş.');
        return trim($metin);
    }
}
