<?php

namespace App\Models;


use Database\Factories\UserFactory;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;



#[Fillable([
    'name',
    'email',
    'password',
    'role'
])]


#[Hidden([
    'password',
    'remember_token'
])]


class User extends Authenticatable
{


    use HasFactory, Notifiable;



    /*
    |--------------------------------------------------------------------------
    | Rol Kontrolleri
    |--------------------------------------------------------------------------
    */


    public function isSistemYoneticisi(): bool
    {

        return $this->role === 'sistem_yoneticisi';

    }




    public function isAdmin(): bool
    {

        return $this->role === 'admin';

    }





    public function isUsta(): bool
    {

        return $this->role === 'usta';

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





    /*
    |--------------------------------------------------------------------------
    | Yetki Grupları
    |--------------------------------------------------------------------------
    */


    public function sistemYetkilisiMi(): bool
    {

        return in_array(

            $this->role,

            [
                'sistem_yoneticisi',
                'admin'
            ]

        );

    }





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





    public function casts(): array
    {

        return [

            'email_verified_at' => 'datetime',

            'password' => 'hashed',

        ];

    }



}

protected $fillable = [

    'username',
    'name',
    'surname',
    'email',
    'phone',
    'tc_no',
    'password',
    'role',
    'status',
    'created_by',

];