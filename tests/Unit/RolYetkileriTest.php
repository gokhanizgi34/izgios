<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RolYetkileriTest extends TestCase
{
    #[DataProvider('ikRolleri')]
    public function test_ik_erisimi_yalniz_yetkili_rollere_aciktir(string $rol, bool $beklenen): void
    {
        $kullanici = new User(['role' => $rol]);

        $this->assertSame($beklenen, $kullanici->ikErisimiVarMi());
    }

    public static function ikRolleri(): array
    {
        return [
            'sistem yöneticisi' => ['sistem_yoneticisi', true],
            'firma sahibi' => ['admin', true],
            'insan kaynakları' => ['ik', true],
            'usta' => ['usta', false],
            'muhasebe' => ['muhasebe', false],
            'yedek parça' => ['yedek_parca', false],
        ];
    }

    #[DataProvider('mobilOturumRolleri')]
    public function test_mobil_oturum_korumasi_yalniz_usta_ve_firma_sahibine_aciktir(string $rol, bool $beklenen): void
    {
        $kullanici = new User(['role' => $rol]);

        $this->assertSame($beklenen, $kullanici->mobilOturumKorunurMu());
    }

    public static function mobilOturumRolleri(): array
    {
        return [
            'usta' => ['usta', true],
            'firma sahibi' => ['admin', true],
            'sistem yöneticisi' => ['sistem_yoneticisi', false],
            'ofis' => ['ofis', false],
            'muhasebe' => ['muhasebe', false],
            'yedek parça' => ['yedek_parca', false],
        ];
    }
}
