<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;



class User extends Authenticatable
{


    use HasFactory, Notifiable;


    public function firmaPersoneli(): HasOne
    {
        return $this->hasOne(FirmaPersonel::class)->where('aktif', true);
    }



    /*
    |--------------------------------------------------------------------------
    | Toplu Atama Alanları
    |--------------------------------------------------------------------------
    */


    protected $fillable = [


        'name',

        'surname',

        'email',

        'phone',

        'tc_no',

        'dogum_tarihi',

        'password',

        'role',

        'status',

        'created_by',


    ];





    /*
    |--------------------------------------------------------------------------
    | Gizli Alanlar
    |--------------------------------------------------------------------------
    */


    protected $hidden = [


        'password',

        'remember_token',


    ];





    /*
    |--------------------------------------------------------------------------
    | Cast Alanları
    |--------------------------------------------------------------------------
    */


    protected function casts(): array
    {

        return [


            'email_verified_at' => 'datetime',

            'dogum_tarihi' => 'date',


            'password' => 'hashed',


        ];

    }





    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Oluşturan
    |--------------------------------------------------------------------------
    */


    public function olusturan()
    {

        return $this->belongsTo(

            User::class,

            'created_by'

        );

    }





    /*
    |--------------------------------------------------------------------------
    | Oluşturduğu Kullanıcılar
    |--------------------------------------------------------------------------
    */


    public function kullanicilar()
    {

        return $this->hasMany(

            User::class,

            'created_by'

        );

    }
        /*
    |--------------------------------------------------------------------------
    | Rol Kontrolleri
    |--------------------------------------------------------------------------
    */


    public function isSistemYoneticisi(): bool
    {

        return $this->role === 'sistem_yoneticisi';

    }

    /** Sistem genelindeki firma, şube, kullanıcı ve teknik yönetim erişimi. */
    public function tamSistemYetkisiVarMi(): bool
    {
        return $this->isSistemYoneticisi();
    }




    public function isAdmin(): bool
    {

        return $this->role === 'admin';

    }





    public function isUsta(): bool
    {

        return $this->role === 'usta';

    }

    /** Mobilde uzun oturum ve son servis ekranına dönüş özelliğini kullanabilen roller. */
    public function mobilOturumKorunurMu(): bool
    {
        return $this->isUsta() || $this->isAdmin();
    }





    public function isOfis(): bool
    {

        return $this->role === 'ofis';

    }





    public function isMuhasebe(): bool
    {

        return $this->role === 'muhasebe';

    }





    public function isYedekParca(): bool
    {

        return $this->role === 'yedek_parca';

    }


    public function isIk(): bool
    {
        return $this->role === 'ik';
    }

    /** İnsan kaynakları çalışma alanında tam işlem yapabilen roller. */
    public function ikErisimiVarMi(): bool
    {
        return $this->tamSistemYetkisiVarMi() || $this->isAdmin() || $this->isIk();
    }





    /*
    |--------------------------------------------------------------------------
    | Yetki Kontrolleri
    |--------------------------------------------------------------------------
    */


    public function yoneticiMi(): bool
    {

        return in_array(

            $this->role,

            [

                'sistem_yoneticisi',

                'admin'

            ]

        );

    }





    public function raporErisimiVarMi(): bool
    {

        return in_array(

            $this->role,

            [

                'sistem_yoneticisi',

                'admin',

                'usta',

                'ofis',

                'muhasebe',

                'yedek_parca'

            ]

        );

    }





    public function servisErisimiVarMi(): bool
    {

        return in_array(

            $this->role,

            [

                'sistem_yoneticisi',

                'admin',

                'usta'

            ]

        );

    }





    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Durum Kontrolleri
    |--------------------------------------------------------------------------
    */


    public function aktifMi(): bool
    {

        return $this->status === 'aktif';

    }





    public function pasifMi(): bool
    {

        return $this->status === 'pasif';

    }
        /*
    |--------------------------------------------------------------------------
    | Kullanıcı Bilgi Yardımcıları
    |--------------------------------------------------------------------------
    */


    public function tamAdi(): string
    {

        return trim(

            $this->name . ' ' . $this->surname

        );

    }





    public function rolAdi(): string
    {

        return match ($this->role) {


            'sistem_yoneticisi' => 'Sistem Yöneticisi',


            'admin' => 'Firma Sahibi',

            'ik' => 'İnsan Kaynakları',


            'usta' => 'Usta',


            'ofis' => 'Ofis',


            'muhasebe' => 'Muhasebe',


            'yedek_parca' => 'Yedek Parça',


            default => 'Kullanıcı',


        };


    }





    /*
    |--------------------------------------------------------------------------
    | Yetki Kontrolü
    |--------------------------------------------------------------------------
    */


    public function yetkisiVarMi(string $yetki): bool
    {

        return $this->role === $yetki;

    }





    /*
    |--------------------------------------------------------------------------
    | Otomatik Veri Düzenleme
    |--------------------------------------------------------------------------
    */


    protected static function boot()
    {

        parent::boot();



        static::creating(function ($user) {


            if (!empty($user->email)) {


                $user->email = mb_strtolower(

                    trim($user->email),

                    'UTF-8'

                );


            }



            if (empty($user->status)) {


                $user->status = 'aktif';


            }


        });





        static::updating(function ($user) {


            if (!empty($user->email)) {


                $user->email = mb_strtolower(

                    trim($user->email),

                    'UTF-8'

                );


            }



        });


    }
        /*
    |--------------------------------------------------------------------------
    | Kullanıcı Yetki Kısa Kontrolleri
    |--------------------------------------------------------------------------
    */


    public function sistemYetkilisiMi(): bool
    {

        return in_array(

            $this->role,

            [

                'sistem_yoneticisi',

                'admin',

                'ik'

            ]

        );

    }





    public function kullaniciYonetebilirMi(): bool
    {

        return in_array(

            $this->role,

            [

                'sistem_yoneticisi',

                'admin',

                'ik'

            ]

        );

    }





    /*
    |--------------------------------------------------------------------------
    | Kullanıcı Durumu
    |--------------------------------------------------------------------------
    */


    public function aktifDurum(): string
    {

        return $this->status === 'aktif'

            ? 'Aktif'

            : 'Pasif';

    }





}
