<?php

namespace App\Http\Controllers;

use App\Models\UsersProfile\UsersProfile;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function dashboard(){
        return view('dashboard');
    }

    public function jadwalTenagaMedis(){
        return view('jadwalTenagaMedis.index');
    }

    public function kunjungan(){
        return view('kunjungan.index');
    }

    public function obat(){
        return view('obat.index');
    }

    public function pasien(){
        return view('pasien.index');
    }

    public function pembayaranPasien(){
        return view('pembayaranPasien.index');
    }

    public function poli(){
        return view('poli.index');
    }

    public function radiologi(){
        return view('radiologi.index');
    }

    public function rawatInap(){
        return view('rawatInap.index');
    }

    public function rekamMedis(){
        return view('rekamMedis.index');
    }

    public function resepobat(){
        return view('resepobat.index');
    }

    public function ruangan(){
        return view('ruangan.index');
    }

    public function tenagaMedis(){
        $profiles = UsersProfile::select('id', 'nickname')->whereDoesntHave('tenagaMedis')
        ->get();
        $Allprofiles = UsersProfile::select('id', 'nickname')
        ->get();
        return view('tenagaMedis.index', compact('profiles', 'Allprofiles'));
    }

    public function usersProfile(){
        return view('usersProfile.index');
    }

    public function user(){
        return view('user.index');
    }

    public function kunjunganUlang(){
        return view('kunjunganUlang.index');
    }

    public function detailPembayaranPasien(){
        return view('detailPembayaranPasien.index');
    }

    public function supplier(){
        return view('supplier.index');
    }

    public function pembelianObat(){
        return view('pembelianObat.index');
    }

    public function labPemeriksaan(){
        return view('labPemeriksaan.index');
    }

    public function detailPembelianObat(){
        return view('detailPembelianObat.index');
    }

    public function pembayaranSupplier(){
        return view('pembayaranSupplier.index');
    }
}

