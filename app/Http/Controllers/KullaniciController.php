<?php

namespace App\Http\Controllers;


use App\Models\Firma;
use App\Models\FirmaPersonel;
use App\Models\Rol;
use App\Models\Sube;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Illuminate\Validation\Rule;
use App\Services\MaasHesaplamaServisi;



class KullaniciController extends Controller
{

    private function roller(): array
    {
        return [
            'sistem_yoneticisi' => 'Sistem Yöneticisi',
            'admin' => 'Firma Sahibi',
            'ik' => 'İnsan Kaynakları',
            'usta' => 'Usta',
            'ofis' => 'Ofis',
            'muhasebe' => 'Muhasebe',
            'yedek_parca' => 'Yedek Parça',
        ];
    }

    private function firmalar(): \Illuminate\Database\Eloquent\Collection
    {
        $sorgu = Firma::query()
            ->where('aktif', true)
            ->with(['subeler' => fn ($query) => $query->where('aktif', true)->orderBy('sube_adi')])
            ->orderBy('unvan');

        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            $sorgu->whereKey($this->aktifFirmaId());
        }

        return $sorgu->get();
    }

    private function baglantiVerisiniDogrula(Request $request): array
    {
        $firma = Firma::query()->where('aktif', true)->findOrFail($request->integer('firma_id'));
        if (! auth()->user()?->tamSistemYetkisiVarMi()) {
            abort_unless($firma->id === $this->aktifFirmaId(), 403, 'Yalnız kendi firmanıza personel bağlayabilirsiniz.');
        }
        if ($request->input('sube_id') === 'firma_merkez') {
            return [$firma, null];
        }

        $subeler = $firma->subeler()->where('aktif', true)->orderBy('sube_adi')->get();

        if ($subeler->isEmpty()) {
            abort(422, 'Seçilen firmaya ait aktif şube bulunmuyor. Önce firma kartından şube ekleyin.');
        }

        $subeId = $request->integer('sube_id');
        $sube = $subeler->firstWhere('id', $subeId);

        if (!$sube) {
            abort(422, 'Lütfen seçilen firmaya ait geçerli bir şube seçin.');
        }

        return [$firma, $sube];
    }

    private function firmaPersoneliniKaydet(User $kullanici, Firma $firma, ?Sube $sube): void
    {
        $rol = Rol::firstOrCreate(
            ['ad' => $this->roller()[$kullanici->role]],
            ['aktif' => true]
        );

        FirmaPersonel::updateOrCreate(
            ['user_id' => $kullanici->id],
            [
                'firma_id' => $firma->id,
                'sube_id' => $sube?->id,
                'rol_id' => $rol->id,
                'ad_soyad' => $kullanici->tamAdi(),
                'telefon' => $kullanici->phone,
                'email' => $kullanici->email,
                'aktif' => $kullanici->status === 'aktif',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Yetki Kontrolü
    |--------------------------------------------------------------------------
    */


    private function yetkisiVarMi(): bool
    {


        if (!auth()->check()) {

            return false;

        }



        return in_array(

            auth()->user()->role,

            [

                'sistem_yoneticisi',

                'admin',

                'ik',

            ]

        );


    }





    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Listesi
    |--------------------------------------------------------------------------
    */


    public function index(Request $request)
    {

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Personel ekranı için oturum açmalısınız.');
        }

        if (!$this->yetkisiVarMi()) {
            abort(403);
        }

        return redirect()->route('kullanicilar.aktifler');
    }


    public function aktifler(Request $request)
    {
        return $this->liste($request, 'aktif');
    }


    public function pasifler(Request $request)
    {
        return $this->liste($request, 'pasif');
    }


    private function liste(Request $request, string $durum)
    {

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Personel ekranı için oturum açmalısınız.');
        }

        if (!$this->yetkisiVarMi()) {
            abort(403);
        }


        $query = User::query();

        $query->where('status', $durum);

        if (! auth()->user()->tamSistemYetkisiVarMi()) {
            $query->whereHas('firmaPersoneli', fn ($personel) => $personel->where('firma_id', $this->aktifFirmaId()));
        }




        if ($request->filled('role')) {


            $query->where(

                'role',

                $request->role

            );


        }




        $kullanicilar = $query

            ->orderBy(

                'created_at',

                'desc'

            )

            ->paginate(20);




        return view('kullanicilar.personel-card-list', compact('kullanicilar', 'durum'));


    }





    /*
    |--------------------------------------------------------------------------
    | Yeni Kullanıcı Formu
    |--------------------------------------------------------------------------
    */


    public function create()
    {


        if (!$this->yetkisiVarMi()) {

            abort(403);

        }

        $roller = $this->roller();
        $firmalar = $this->firmalar();

        return view('kullanicilar.personel-create-v2', compact('roller', 'firmalar'));


    }





    /*
    |--------------------------------------------------------------------------
    | Yeni Kullanıcı Kaydet
    |--------------------------------------------------------------------------
    */


    public function store(Request $request)
    {


        if (!$this->yetkisiVarMi()) {

            abort(403);

        }

        if (auth()->user()->isIk() && in_array($request->input('role'), ['sistem_yoneticisi', 'admin'], true)) {
            abort(403, 'İK rolü Sistem Yöneticisi veya Firma Sahibi oluşturamaz.');
        }



        $validated = $request->validate([


            'name' => [

                'required',

                'string',

                'max:100',

            ],



            'surname' => [

                'required',

                'string',

                'max:100',

            ],



            'email' => [

                'required',

                'email',

                'unique:users,email',

            ],

                        'phone' => [

                'nullable',

                'string',

                'max:20',

            ],



            'tc_no' => [

                'nullable',

                'string',

                'max:11',

            ],

            'dogum_tarihi' => ['nullable', 'date', 'before:today'],

            'firma_id' => ['required', 'integer', 'exists:firmas,id'],

            'sube_id' => ['required'],

            'brut_ucret' => ['nullable', 'numeric', 'min:0'],
            'net_ucret' => ['nullable', 'numeric', 'min:0'],
            'ucret_giris_turu' => ['nullable', Rule::in(['brut', 'net'])],
            'calisma_baslangic' => ['nullable', 'date_format:H:i'],
            'calisma_bitis' => ['nullable', 'date_format:H:i', 'after:calisma_baslangic'],



            'password' => [

                'required',

                'min:8',

                'confirmed',

            ],



            'role' => [

                'required',

                Rule::in([

                    'sistem_yoneticisi',

                    'admin',

                    'ik',

                    'usta',

                    'ofis',

                    'muhasebe',

                    'yedek_parca',

                ]),

            ],



        ], $this->dogrulamaMesajlari());





        /*
        |--------------------------------------------------------------------------
        | Kullanıcı bilgileri normalize
        |--------------------------------------------------------------------------
        */


        $validated['email'] = mb_strtolower(

            trim($validated['email']),

            'UTF-8'

        );





        /*
        |--------------------------------------------------------------------------
        | Şifre oluştur
        |--------------------------------------------------------------------------
        */


        $validated['password'] = Hash::make(

            $validated['password']

        );





        /*
        |--------------------------------------------------------------------------
        | Sistem alanları
        |--------------------------------------------------------------------------
        */


        $validated['status'] = 'aktif';


        $validated['created_by'] = auth()->id();





        [$firma, $sube] = $this->baglantiVerisiniDogrula($request);

        $ikVerisi = collect($validated)->only(['brut_ucret','net_ucret','ucret_giris_turu','calisma_baslangic','calisma_bitis'])->all();
        $ucretTuru = $ikVerisi['ucret_giris_turu'] ?? (filled($ikVerisi['net_ucret'] ?? null) ? 'net' : 'brut');
        if (filled($ikVerisi[$ucretTuru.'_ucret'] ?? null)) {
            $hesaplayici = app(MaasHesaplamaServisi::class);
            $hesap = $ucretTuru === 'net'
                ? $hesaplayici->nettenBrute((float)$ikVerisi['net_ucret'])
                : $hesaplayici->bruttenNete((float)$ikVerisi['brut_ucret']);
            $ikVerisi['brut_ucret'] = $hesap['brut'];
            $ikVerisi['net_ucret'] = $hesap['net'];
        }
        unset($ikVerisi['ucret_giris_turu']);
        DB::transaction(function () use ($validated, $firma, $sube, $ikVerisi) {
            unset($validated['firma_id'], $validated['sube_id'], $validated['brut_ucret'], $validated['net_ucret'], $validated['ucret_giris_turu'], $validated['calisma_baslangic'], $validated['calisma_bitis']);
            $kullanici = User::create($validated);
            $this->firmaPersoneliniKaydet($kullanici, $firma, $sube);
            if (collect($ikVerisi)->filter(fn ($deger) => filled($deger))->isNotEmpty()) {
                DB::table('ik_personel_ozlukleri')->insert(array_merge([
                    'firma_id' => $firma->id,
                    'user_id' => $kullanici->id,
                    'puantaj_qr_token' => (string) \Illuminate\Support\Str::uuid(),
                    'puantaj_qr_yenilendi_at' => now(),
                    'calisma_gunleri' => json_encode([1,2,3,4,5,6]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ], array_filter($ikVerisi, fn ($deger) => filled($deger))));
                $brut = (float) ($ikVerisi['brut_ucret'] ?? 0);
                $net = (float) ($ikVerisi['net_ucret'] ?? 0);
                DB::table('ik_bordrolar')->insert([
                    'firma_id'=>$firma->id,'user_id'=>$kullanici->id,'donem'=>now()->startOfMonth()->toDateString(),
                    'brut_ucret'=>$brut,'net_ucret'=>$net,'esas_net_ucret'=>$net,'calisilan_gun'=>30,'odenecek_net'=>$net,
                    'durum'=>'taslak','aciklama'=>'Personel oluşturulurken ücret kartından otomatik açıldı; İK puantaj hesabında güncellenecek.',
                    'olusturan_id'=>auth()->id(),'created_at'=>now(),'updated_at'=>now(),
                ]);
            }
        });





        return redirect()

            ->route('kullanicilar.index')

            ->with(

                'success',

                'Personel başarıyla oluşturuldu.'

            );


    }





    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Düzenleme Formu
    |--------------------------------------------------------------------------
    */


    public function edit(User $kullanici)
    {


        if (!$this->yetkisiVarMi()) {

            abort(403);

        }

        $this->kullaniciFirmaErisiminiDogrula($kullanici);





        $roller = $this->roller();
        $firmalar = $this->firmalar();
        $personelBaglantisi = FirmaPersonel::query()
            ->where('user_id', $kullanici->id)
            ->first();

        return view('kullanicilar.personel-edit-v2', compact(
            'kullanici', 'roller', 'firmalar', 'personelBaglantisi'
        ));


    }





    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Güncelleme
    |--------------------------------------------------------------------------
    */


    public function update(Request $request, User $kullanici)
    {


        if (!$this->yetkisiVarMi()) {

            abort(403);

        }

        $this->kullaniciFirmaErisiminiDogrula($kullanici);

        if (auth()->user()->isIk() && ($kullanici->isSistemYoneticisi() || $kullanici->isAdmin() || in_array($request->input('role'), ['sistem_yoneticisi', 'admin'], true))) {
            abort(403, 'İK rolü Sistem Yöneticisi veya Firma Sahibi hesabını değiştiremez.');
        }





        $validated = $request->validate([
            'name' => [

                'required',

                'string',

                'max:100',

            ],



            'surname' => [

                'required',

                'string',

                'max:100',

            ],



            'email' => [

                'required',

                'email',

                Rule::unique('users','email')

                    ->ignore($kullanici->id),

            ],



            'phone' => [

                'nullable',

                'string',

                'max:20',

            ],



            'tc_no' => [

                'nullable',

                'string',

                'max:11',

            ],

            'dogum_tarihi' => ['nullable', 'date', 'before:today'],

            'firma_id' => ['required', 'integer', 'exists:firmas,id'],

            'sube_id' => ['required'],



            'role' => [

                'required',

                Rule::in([

                    'sistem_yoneticisi',

                    'admin',

                    'ik',

                    'usta',

                    'ofis',

                    'muhasebe',

                    'yedek_parca',

                ]),

            ],



            'password' => [

                'nullable',

                'min:8',

                'confirmed',

            ],



        ], $this->dogrulamaMesajlari());





        /*
        |--------------------------------------------------------------------------
        | Güncelleme normalize
        |--------------------------------------------------------------------------
        */


        $validated['email'] = mb_strtolower(

            trim($validated['email']),

            'UTF-8'

        );





        /*
        |--------------------------------------------------------------------------
        | Şifre güncelleme
        |--------------------------------------------------------------------------
        */


        if (!empty($validated['password'])) {


            $validated['password'] = Hash::make(

                $validated['password']

            );


        } else {


            unset($validated['password']);


        }





        [$firma, $sube] = $this->baglantiVerisiniDogrula($request);

        DB::transaction(function () use ($kullanici, $validated, $firma, $sube) {
            unset($validated['firma_id'], $validated['sube_id']);
            $kullanici->update($validated);
            $this->firmaPersoneliniKaydet($kullanici->fresh(), $firma, $sube);
        });





        return redirect()

            ->route('kullanicilar.index')

            ->with(

                'success',

                'Personel bilgileri güncellendi.'

            );


    }





    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Pasif Yap
    |--------------------------------------------------------------------------
    */


    public function pasifYap(User $kullanici)
    {


        if (!$this->yetkisiVarMi()) {

            abort(403);

        }

        $this->kullaniciFirmaErisiminiDogrula($kullanici);





        $kullanici->update([

            'status' => 'pasif'

        ]);





        return back()

            ->with(

                'success',

                'Personel pasif duruma getirildi.'

            );


    }





    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Aktif Yap
    |--------------------------------------------------------------------------
    */


    public function aktifYap(User $kullanici)
    {


        if (!$this->yetkisiVarMi()) {

            abort(403);

        }

        $this->kullaniciFirmaErisiminiDogrula($kullanici);





        $kullanici->update([

            'status' => 'aktif'

        ]);





        return back()

            ->with(

                'success',

                'Personel aktif duruma getirildi.'

            );


    }
        /*
    |--------------------------------------------------------------------------
    | Kullanıcı Silme
    |--------------------------------------------------------------------------
    |
    | İZGİOS sisteminde kullanıcı silinmez.
    | Geçmiş servis ve işlem kayıtlarının korunması için
    | aktif/pasif yöntemi kullanılır.
    |
    |--------------------------------------------------------------------------
    */


    public function destroy(User $kullanici)
    {


        return back()

            ->with(

                'error',

                'Personel silme işlemi kapalıdır. Pasif duruma alabilirsiniz.'

            );


    }

    private function dogrulamaMesajlari(): array
    {
        return [
            'required' => ':attribute zorunludur.',
            'email' => 'Geçerli bir e-posta adresi yazın.',
            'unique' => 'Bu e-posta adresi sistemde zaten kayıtlı.',
            'min' => ':attribute en az :min karakter olmalıdır.',
            'confirmed' => 'Şifre ve şifre tekrarı aynı olmalıdır.',
            'exists' => 'Seçilen :attribute bulunamadı.',
        ];
    }

    private function aktifFirmaId(): ?int
    {
        return session('aktif_firma_id') ?: auth()->user()?->firmaPersoneli?->firma_id;
    }

    private function kullaniciFirmaErisiminiDogrula(User $kullanici): void
    {
        if (auth()->user()?->tamSistemYetkisiVarMi()) {
            return;
        }

        abort_unless(
            FirmaPersonel::query()
                ->where('user_id', $kullanici->id)
                ->where('firma_id', $this->aktifFirmaId())
                ->where('aktif', true)
                ->exists(),
            403,
            'Bu personel başka bir firmaya bağlıdır.'
        );
    }



}
