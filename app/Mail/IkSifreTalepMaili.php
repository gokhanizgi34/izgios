<?php

namespace App\Mail;

use App\Models\SifreYenilemeTalebi;
use Illuminate\Mail\Mailable;

class IkSifreTalepMaili extends Mailable
{
    public function __construct(public SifreYenilemeTalebi $talep) {}
    public function build(): self { return $this->subject('Şifre yenileme talebi | İZGİOS')->view('emails.ik-sifre-talebi'); }
}
