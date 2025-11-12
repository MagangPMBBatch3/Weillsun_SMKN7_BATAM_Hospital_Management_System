<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan\Kunjungan;
use App\Models\Obat\Obat;
use App\Models\Poli\Poli;
use App\Models\TenagaMedis\TenagaMedis;
use App\Models\UsersProfile\UsersProfile;
use Illuminate\Http\Request;
use App\Models\Pasien\Pasien;

class AuthController extends Controller
{

    public function dashboard()
    {

        return view('dashboard');
    }

    public function jadwalTenagaMedis()
    {
        return view('jadwalTenagaMedis.index');
    }

    public function kunjungan()
    {
        $pasiens = Pasien::select('id', 'nama')->whereDoesntHave('kunjungan')->get();
        $Allpasiens = Pasien::select('id', 'nama')->get();

        $Allpolies = Poli::select('id', 'nama_poli')->get();

        return view('kunjungan.index', compact(
            'pasiens',
            'Allpasiens',
            'Allpolies'
        ));
    }

    public function obat()
    {
        return view('obat.index');
    }

    public function pasien()
    {
        return view('pasien.index');
    }

    public function pembayaranPasien()
    {
        return view('pembayaranPasien.index');
    }

    public function poli()
    {
        return view('poli.index');
    }

    public function radiologi()
    {
        return view('radiologi.index');
    }

    public function rawatInap()
    {
        return view('rawatInap.index');
    }

    public function rekamMedis()
    {
        $pasiens = Pasien::select('id', 'nama')->whereDoesntHave('rekamMedis')->get();
        $Allpasiens = Pasien::select('id', 'nama')->get();

        // $tenagaMedises = TenagaMedis::with('profile:id,nickname')
        //     ->whereDoesntHave('rekamMedis')
        //     ->get();
        $AlltenagaMedises = TenagaMedis::with('profile:id,nickname')
            ->get();

        return view('rekamMedis.index', compact('pasiens', 'Allpasiens', 'AlltenagaMedises'));
    }

    public function resepobat()
    {
        $pasiens = Pasien::select('id', 'nama')->whereDoesntHave('rekamMedis')->get();
        $Allpasiens = Pasien::select('id', 'nama')->get();

        // $tenagaMedises = TenagaMedis::with('profile:id,nickname')
        //     ->whereDoesntHave('rekamMedis')
        //     ->get();
        $AlltenagaMedises = TenagaMedis::with('profile:id,nickname')
            ->get();
        
        $obats = Obat::select('id', 'nama_obat')->get();
        
        return view('resepobat.index', compact('pasiens', 'Allpasiens', 'AlltenagaMedises', 'obats'));
    }

    public function ruangan()
    {
        return view('ruangan.index');
    }

    public function tenagaMedis()
    {
        $profiles = UsersProfile::select('id', 'nickname')->whereDoesntHave('tenagaMedis')
            ->get();
        $Allprofiles = UsersProfile::select('id', 'nickname')
            ->get();
        return view('tenagaMedis.index', compact('profiles', 'Allprofiles'));
    }

    public function usersProfile()
    {
        return view('usersProfile.index');
    }

    public function user()
    {
        return view('user.index');
    }

    public function kunjunganUlang()
    {
        return view('kunjunganUlang.index');
    }

    public function detailPembayaranPasien()
    {
        return view('detailPembayaranPasien.index');
    }

    public function supplier()
    {
        return view('supplier.index');
    }

    public function pembelianObat()
    {
        return view('pembelianObat.index');
    }

    public function labPemeriksaan()
    {
        return view('labPemeriksaan.index');
    }

    public function detailPembelianObat()
    {
        return view('detailPembelianObat.index');
    }

    public function pembayaranSupplier()
    {
        return view('pembayaranSupplier.index');
    }
}
