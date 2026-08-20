<?php

namespace App\Http\Controllers;


use App\Models\Arac;
use Illuminate\Support\Facades\DB;



class QrServisController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | QR Servis Görüntüleme
    |--------------------------------------------------------------------------
    */


    public function show($token)
    {



        $arac = Arac::with([


            'musteri',


            'servisler' => function($query){

                $query->orderBy(
                    'created_at',
                    'desc'
                );

            },


            'servisler.fotograflar',


            'servisler.islemler',


            'servisler.parcalar'

            ,'servisler.sube'


        ])

        ->where(
            'qr_token',
            $token
        )

        ->firstOrFail();





        /*
        |--------------------------------------------------------------------------
        | Müşteri Bilgileri
        |--------------------------------------------------------------------------
        */


        $musteri = null;



        if($arac->musteri)
        {


            $musteri = [


                'ad_soyad' => $this->isimMaskele(

                    $arac->musteri->ad_soyad

                ),



                'telefon' => $this->telefonMaskele(

                    $arac->musteri->telefon

                ),



            ];


        }






        /*
        |--------------------------------------------------------------------------
        | Bir Sonraki Bakım
        |--------------------------------------------------------------------------
        */


        $sonrakiBakim = $arac
            ->servisler
            ->whereNotNull(
                'sonraki_bakim_tarihi'
            )
            ->sortBy(
                'sonraki_bakim_tarihi'
            )
            ->first();



        /*
        |--------------------------------------------------------------------------
        | QR Sayfası
        |--------------------------------------------------------------------------
        */
        $kullanici = auth()->user();
        $aktifFirmaId = session('aktif_firma_id') ?: $kullanici?->firmaPersoneli?->firma_id;
        $hizliIslemYetkisi = $kullanici
            && ($kullanici->isUsta() || $kullanici->isAdmin())
            && $aktifFirmaId
            && (int) $aktifFirmaId === (int) $arac->firma_id;

        $periyodikBakimlar = $arac->servisler
            ->flatMap(fn ($servis) => $servis->islemler
                ->where('kategori', 'periyodik_bakim')
                ->map(fn ($islem) => ['servis' => $servis, 'islem' => $islem]))
            ->sortByDesc(fn ($kayit) => $kayit['servis']->servis_tarihi ?? $kayit['servis']->created_at)
            ->values();

        // QR servis sekmesi yalnızca iş emrindeki "Yapılan İşlemler"
        // kayıtlarından, bakım sekmesi ise yalnız periyodik bakım kayıtlarından beslenir.
        $servisIslemleri = $arac->servisler
            ->map(function ($servis) {
                return [
                    'servis' => $servis,
                    'islemler' => $servis->islemler
                        ->where('kategori', '!=', 'periyodik_bakim')
                        ->values(),
                ];
            })
            ->filter(fn ($kayit) => $kayit['islemler']->isNotEmpty())
            ->values();

        $firmaId = (int) ($arac->servisler->first()?->firma_id ?: $arac->firma_id);
        $whatsappEntegrasyonu = DB::table('muhasebe_entegrasyonlari')
            ->where('firma_id', $firmaId)
            ->where('saglayici', 'whatsapp')
            ->where('aktif', true)
            ->first();
        $whatsappAyarlari = json_decode($whatsappEntegrasyonu?->ayarlar ?: '{}', true) ?: [];
        $whatsappNo = preg_replace('/\D+/', '', (string) ($whatsappAyarlari['gonderen'] ?? ''));
        if (str_starts_with($whatsappNo, '0')) {
            $whatsappNo = '90'.substr($whatsappNo, 1);
        }
        $whatsappMesaj = "Merhaba, {$arac->plaka} plakalı aracımla ilgili servis kaydı hakkında bilgi almak istiyorum.";
        $whatsappUrl = strlen($whatsappNo) >= 10
            ? 'https://wa.me/'.$whatsappNo.'?text='.rawurlencode($whatsappMesaj)
            : null;

        return view(

            'qr.musteri-servis-v4',

            compact(

                'arac',

                'musteri',

                'sonrakiBakim',
                'periyodikBakimlar',
                'servisIslemleri',
                'whatsappUrl',
                'hizliIslemYetkisi'

            )

        );


    }






    /*
    |--------------------------------------------------------------------------
    | İsim Maskeleme
    |--------------------------------------------------------------------------
    */


    private function isimMaskele($isim)
    {


        if(!$isim)
        {

            return null;

        }




        $parcalar = explode(

            ' ',

            trim($isim)

        );




        return collect($parcalar)

            ->map(function($kelime){


                return mb_substr(

                    $kelime,

                    0,

                    1

                )

                .

                str_repeat(

                    '*',

                    max(

                        mb_strlen($kelime)-1,

                        1

                    )

                );


            })

            ->implode(' ');



    }







    /*
    |--------------------------------------------------------------------------
    | Telefon Maskeleme
    |--------------------------------------------------------------------------
    */


    private function telefonMaskele($telefon)
    {


        if(!$telefon)
        {

            return null;

        }




        return substr(

            $telefon,

            0,

            4

        )

        .

        ' *** ** '

        .

        substr(

            $telefon,

            -2

        );


    }





}
