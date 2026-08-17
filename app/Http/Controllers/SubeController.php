<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use App\Models\Sube;
use Illuminate\Http\Request;

class SubeController extends Controller
{
    public function index(Firma $firma)
    {
        $subeler = $firma->subeler()
            ->withCount('personeller')
            ->orderBy('sube_adi')
            ->get();

        return view('ayarlar.firma.sube.list', compact('firma', 'subeler'));
    }

    public function create(Firma $firma)
    {
        return view('ayarlar.firma.sube.create', compact('firma'));
    }

    public function store(Request $request, Firma $firma)
    {
        $firma->subeler()->create([
            ...$request->validate($this->rules()),
            'aktif' => true,
        ]);

        return redirect()->route('sube.index', $firma)->with('success', 'Şube oluşturuldu.');
    }

    public function show(Firma $firma, Sube $sube)
    {
        $this->ensureOwnership($firma, $sube);
        $sube->loadCount('personeller');

        return view('ayarlar.firma.sube.detail', compact('firma', 'sube'));
    }

    public function edit(Firma $firma, Sube $sube)
    {
        $this->ensureOwnership($firma, $sube);

        return view('ayarlar.firma.sube.edit', compact('firma', 'sube'));
    }

    public function update(Request $request, Firma $firma, Sube $sube)
    {
        $this->ensureOwnership($firma, $sube);
        $sube->update($request->validate($this->rules()));

        return redirect()->route('sube.show', compact('firma', 'sube'))->with('success', 'Şube güncellendi.');
    }

    public function durumDegistir(Firma $firma, Sube $sube)
    {
        $this->ensureOwnership($firma, $sube);
        $sube->update(['aktif' => ! $sube->aktif]);

        return back()->with('success', 'Şube durumu güncellendi.');
    }

    public function destroy(Firma $firma, Sube $sube)
    {
        $this->ensureOwnership($firma, $sube);

        if ($sube->personeller()->exists()) {
            return back()->with('error', 'Personeli bulunan şube silinemez.');
        }

        $sube->delete();

        return redirect()->route('sube.index', $firma)->with('success', 'Şube silindi.');
    }

    private function rules(): array
    {
        return [
            'sube_adi' => ['required', 'string', 'max:255'],
            'vergi_no' => ['nullable', 'string', 'max:50'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'adres' => ['nullable', 'string'],
        ];
    }

    private function ensureOwnership(Firma $firma, Sube $sube): void
    {
        abort_unless((int) $sube->firma_id === (int) $firma->id, 404);
    }
}
