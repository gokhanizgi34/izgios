<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; use Illuminate\Support\Facades\DB; use Illuminate\Validation\Rule;
class HesapController extends Controller {
private function user(){abort_unless(auth()->check(),403);return auth()->user();}
public function profil(){return view('hesabim.profil-v2',['kullanici'=>$this->user()]);}
public function profilGuncelle(Request $r){$u=$this->user();$v=$r->validate(['name'=>['required','string','max:100'],'surname'=>['required','string','max:100'],'email'=>['required','email',Rule::unique('users','email')->ignore($u->id)],'phone'=>['nullable','string','max:20'],'dogum_tarihi'=>['nullable','date','before:today']]);$u->update($v);return back()->with('success','Profil bilgileriniz güncellendi.');}
public function tercihler(){ $u=$this->user();$tercih=DB::table('kullanici_tercihleri')->where('user_id',$u->id)->first();return view('hesabim.tercihler',compact('tercih'));}
public function tercihGuncelle(Request $r){$u=$this->user();$v=$r->validate(['tema'=>['required','in:acik,koyu,sistem'],'e_posta_bildirimleri'=>['nullable','boolean'],'sistem_bildirimleri'=>['nullable','boolean']]);$v['e_posta_bildirimleri']=$r->boolean('e_posta_bildirimleri');$v['sistem_bildirimleri']=$r->boolean('sistem_bildirimleri');DB::table('kullanici_tercihleri')->updateOrInsert(['user_id'=>$u->id],array_merge($v,['updated_at'=>now(),'created_at'=>now()]));return back()->with('success','Tercihleriniz kaydedildi.');}}
