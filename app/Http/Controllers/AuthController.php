<?php

namespace App\Http\Controllers;

use App\Models\Obat\Obat;
use App\Models\Poli\Poli;
use Illuminate\Http\Request;
use App\Models\Pasien\Pasien;
use App\Models\Ruangan\Ruangan;
use App\Models\Supplier\Supplier;
use App\Models\Kunjungan\Kunjungan;
use App\Models\Radiologi\Radiologi;
use App\Models\RawatInap\RawatInap;
use App\Models\ResepObat\ResepObat;
use App\Models\LogRuangan\LogRuangan;
use App\Models\RekamMedis\RekamMedis;
use App\Models\LogStokObat\LogStokObat;
use App\Models\TenagaMedis\TenagaMedis;
use App\Models\UsersProfile\UsersProfile;
use App\Models\PembelianObat\PembelianObat;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use App\Models\PembayaranPasien\PembayaranPasien;
use App\Models\PembayaranSupplier\PembayaranSupplier;
use App\Models\DetailPembelianObat\DetailPembelianObat;
use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;

class AuthController extends Controller
{

    public function dashboard()
    {

        return view('dashboard');
    }

    public function jadwalTenagaMedis()
    {
        $dokters = TenagaMedis::with('profile:id,nickname')
            ->get();

        $poli = Poli::select('id', 'nama_poli')->get();
        return view('jadwalTenagaMedis.index', compact('dokters', 'poli'));
    }

    public function liburTenagaMedis()
    {
        $dokters = TenagaMedis::with('profile:id,nickname')
            ->get();

        return view('liburTenagaMedis.index', compact('dokters'));
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
        $pasienTerpakai = PembayaranPasien::withTrashed()->pluck('pasien_id');
        $pasiens = Pasien::whereNotIn('id', $pasienTerpakai)
            ->select('id', 'nama')
            ->get();

        $Allpasiens = Pasien::select('id', 'nama')
            ->get();
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

        $ruangan = Ruangan::query()
            ->where('status', 'tersedia')
            ->whereNull('deleted_at')
            ->select('id', 'nama_ruangan')
            ->get();

        $ruanganAll = Ruangan::whereNull('deleted_at')
            ->select('id', 'nama_ruangan', 'status')
            ->get();


        return view('rawatInap.index', compact('pasiens', 'Allpasiens', 'ruangan', 'ruanganAll'));
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

        $AlltenagaMedises = TenagaMedis::with('profile:id,nickname')
            ->get();

        $obats = Obat::select('id', 'nama_obat', 'stok')->get();

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
        $pasiens = Kunjungan::select('id', 'pasien_id')
            ->with('pasien:id,nama')
            ->get();
        return view('kunjunganUlang.index', compact('pasiens'));
    }

    public function detailPembayaranPasien()
    {
        $pasienTerpakai = DetailPembayaranPasien::withTrashed()->pluck('pembayaran_id');
        $pasiens = PembayaranPasien::select('id', 'pasien_id', 'tanggal_bayar')
            ->with('pasien:id,nama')
            ->get();

        return view('detailPembayaranPasien.index', compact('pasiens',));
    }

    public function supplier()
    {
        return view('supplier.index');
    }

    public function pembelianObat()
    {
        $suppliers = Supplier::select('id', 'nama_supplier')->get();

        return view('pembelianObat.index', compact('suppliers'));
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
        // Semua pembelian tanpa filter
        $pembelians = PembelianObat::select('id', 'supplier_id', 'tanggal')
            ->with('supplier:id,nama_supplier')
            ->get();

        $Allobats = Obat::select('id', 'nama_obat', 'harga')->get();

        return view('detailPembelianObat.index', compact('pembelians', 'Allobats'));
    }


    public function pembayaranSupplier()
    {
        $pembelianTerpakai = PembayaranSupplier::withTrashed()->pluck('pembelian_id');

        $pembelians = PembelianObat::whereNotIn('id', $pembelianTerpakai)
            ->select('id', 'supplier_id', 'total_biaya', 'tanggal')
            ->with('supplier:id,nama_supplier')
            ->get();


        $Allpembelians = PembelianObat::select('id', 'supplier_id', 'total_biaya')
            ->with('supplier:id,nama_supplier')
            ->get();


        return view('pembayaranSupplier.index', compact('pembelians', 'Allpembelians'));
    }

    public function logRuangan()
    {
        $logs = LogRuangan::with(['ruangan:id,nama_ruangan', 'rawatInap:id,tanggal_masuk,tanggal_keluar', 'pasien:id,nama'])
            ->latest('waktu')
            ->paginate(5);

        return view('logRuangan.index', compact('logs'));
    }

    public function logStokObat()
    {
        $logs = LogStokObat::with(
            ['obat:id,nama_obat', 'pembelianObat:id,supplier_id,tanggal', 'pembelianObat.supplier:id,nama_supplier']
        )
            ->latest()
            ->paginate(5);

        return view('logStokObat.index', compact('logs'));
    }
}
