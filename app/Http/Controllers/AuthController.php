<?php

namespace App\Http\Controllers;

use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;
use App\Models\Obat\Obat;
use App\Models\Poli\Poli;
use Illuminate\Http\Request;
use App\Models\Pasien\Pasien;
use App\Models\Ruangan\Ruangan;
use App\Models\Kunjungan\Kunjungan;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use App\Models\PembayaranPasien\PembayaranPasien;
use App\Models\Radiologi\Radiologi;
use App\Models\RawatInap\RawatInap;
use App\Models\RekamMedis\RekamMedis;
use App\Models\ResepObat\ResepObat;
use App\Models\TenagaMedis\TenagaMedis;
use App\Models\UsersProfile\UsersProfile;

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
        $pasienTerpakai = Kunjungan::withTrashed()->pluck('pasien_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

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
        $pasienTerpakai = Radiologi::withTrashed()->pluck('pasien_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

        $Allpasiens = Pasien::select('id', 'nama')->get();
        return view('pembayaranPasien.index', compact('pasiens', 'Allpasiens'));
    }

    public function poli()
    {
        return view('poli.index');
    }

    public function radiologi()
    {
        $pasienTerpakai = Radiologi::withTrashed()->pluck('pasien_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

        $Allpasiens = Pasien::select('id', 'nama')->get();

        $AlltenagaMedises = TenagaMedis::with('profile:id,nickname')
            ->get();

        return view('radiologi.index', compact('pasiens', 'AlltenagaMedises', 'Allpasiens'));
    }

    public function rawatInap()
    {
        $pasienTerpakai = RawatInap::withTrashed()->pluck('pasien_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

        $Allpasiens = Pasien::select('id', 'nama')->get();

        $ruangan = Ruangan::select('id', 'nama_ruangan')->get();

        return view('rawatInap.index', compact('pasiens', 'Allpasiens', 'ruangan'));
    }

    public function rekamMedis()
    {
        $pasienTerpakai = RekamMedis::withTrashed()->pluck('pasien_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

        $Allpasiens = Pasien::select('id', 'nama')->get();

        $AlltenagaMedises = TenagaMedis::with('profile:id,nickname')
            ->get();

        return view('rekamMedis.index', compact('pasiens', 'Allpasiens', 'AlltenagaMedises'));
    }

    public function resepobat()
    {
        $pasienTerpakai = ResepObat::withTrashed()->pluck('pasien_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

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
        $profileTerpakai = TenagaMedis::withTrashed()->pluck('profile_id');
        $profiles = UsersProfile::whereNotIn('id', $profileTerpakai)
            ->select('id', 'nickname')
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
        $pasienTerpakai = DetailPembayaranPasien::withTrashed()->pluck('pembayaran_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

        $Allpasiens = Pasien::select('id', 'nama')->get();

        return view('detailPembayaranPasien.index', compact('pasiens', 'Allpasiens'));
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
        $pasienTerpakai = LabPemeriksaan::withTrashed()->pluck('pasien_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

        $Allpasiens = Pasien::select('id', 'nama')->get();

        $AlltenagaMedises = TenagaMedis::with('profile:id,nickname')
            ->get();

        return view('labPemeriksaan.index', compact('pasiens', 'AlltenagaMedises', 'Allpasiens'));
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
