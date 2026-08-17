<?php

namespace App\Http\Controllers;

use App\Models\Firma;
use Illuminate\Http\Request;

class FirmaYonetimController extends Controller
{
    public function index()
    {
        $firmalar = Firma::query()
            ->withCount(['subeler', 'personeller'])
            ->orderBy('unvan')
            ->get();

        return view('ayarlar.firma.list', compact('firmalar'));
    }

    public function create()
    {
        return view('ayarlar.firma.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        Firma::create([
            ...$validated,
            'merkez_goster' => $request->boolean('merkez_goster'),
            'aktif' => true,
        ]);

        return redirect()->route('firma.index')->with('success', 'Firma oluşturuldu.');
    }

    public function show(Firma $firma)
    {
        $firma->loadCount(['subeler', 'personeller']);

        return view('ayarlar.firma.detail', compact('firma'));
    }

    public function edit(Firma $firma)
    {
        return view('ayarlar.firma.edit', compact('firma'));
    }

    public function update(Request $request, Firma $firma)
    {
        $validated = $request->validate($this->rules());

        $firma->update([
            ...$validated,
            'merkez_goster' => $request->boolean('merkez_goster'),
        ]);

        return redirect()->route('firma.show', $firma)->with('success', 'Firma güncellendi.');
    }

    public function durumDegistir(Firma $firma)
    {
        $firma->update(['aktif' => ! $firma->aktif]);

        return back()->with('success', 'Firma durumu güncellendi.');
    }

    public function destroy(Firma $firma)
    {
        if ($firma->subeler()->exists() || $firma->personeller()->exists()) {
            return back()->with('error', 'Şubesi veya personeli bulunan firma silinemez.');
        }

        $firma->delete();

        return redirect()->route('firma.index')->with('success', 'Firma silindi.');
    }

    private function rules(): array
    {
        return [
            'unvan' => ['required', 'string', 'max:255'],
            'vergi_no' => ['nullable', 'string', 'max:50'],
            'telefon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'adres' => ['nullable', 'string'],
        ];
    }
}
