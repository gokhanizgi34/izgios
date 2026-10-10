<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DogumGunuKutlamaMaili extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $ad, public string $hitap)
    {
    }

    public function build(): self
    {
        return $this->subject('Doğum gününüz kutlu olsun | İZGİOS')
            ->view('emails.dogum-gunu-kutlama');
    }
}
