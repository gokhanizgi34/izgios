<?php

namespace Tests\Feature;

use App\Services\GorselBelgeOkumaServisi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GorselBelgeOkumaServisiTest extends TestCase
{
    public function test_plaka_gorselini_yapilandirilmis_json_olarak_okur(): void
    {
        config(['services.izgios_ai.provider'=>'openai','services.izgios_ai.key'=>'test-key','services.izgios_ai.model'=>'test-vision-model']);
        Http::fake(['api.openai.com/*'=>Http::response(['output'=>[['content'=>[['text'=>json_encode(['plaka'=>'34ABC123','guven'=>.98,'alternatifler'=>[]])]]]]],200)]);
        $yol = tempnam(sys_get_temp_dir(), 'izgios-image-');
        file_put_contents($yol, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
        try {
            $sonuc = app(GorselBelgeOkumaServisi::class)->oku(new UploadedFile($yol,'plaka.png','image/png',null,true),'plaka');
            $this->assertSame('34ABC123',$sonuc['plaka']);
            Http::assertSent(fn($istek)=>$istek->url()==='https://api.openai.com/v1/responses' && data_get($istek->data(),'store')===false && str_starts_with(data_get($istek->data(),'input.0.content.1.image_url'),'data:image/png;base64,'));
        } finally {
            @unlink($yol);
        }
    }
}
