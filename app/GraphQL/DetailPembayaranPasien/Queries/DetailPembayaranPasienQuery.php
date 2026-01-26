<?php

namespace App\GraphQL\DetailPembayaranPasien\Queries;

use Carbon\Carbon;
use App\Models\Obat\Obat;
use App\Models\Kunjungan\Kunjungan;
use App\Models\Radiologi\Radiologi;
use App\Models\RawatInap\RawatInap;
use App\Models\ResepObat\ResepObat;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;

class DetailPembayaranPasienQuery
{
    public function getUnpaidCostsByPasien($_, array $args)
    {
        $pasien_id = $args['pasien_id'] ?? null;

        if (!$pasien_id) {
            return [];
        }

        $unpaidCosts = [];

        // 1. Ambil data Kunjungan yang belum dibayar (is_paid = 0)
        $kunjungans = Kunjungan::where('pasien_id', $pasien_id)
            ->where('is_paid', 0)
            ->with('poli')
            ->get();

        foreach ($kunjungans as $kunjungan) {
            $tanggal = $kunjungan->tanggal_kunjungan instanceof Carbon 
                ? $kunjungan->tanggal_kunjungan 
                : Carbon::parse($kunjungan->tanggal_kunjungan);

            $unpaidCosts[] = [
                'id' => $kunjungan->id,
                'type' => 'kunjungan',
                'type_label' => 'Konsultasi',
                'description' => 'Konsultasi di Poli ' . ($kunjungan->poli->nama ?? ''),
                'jumlah' => 1,
                'harga_satuan' => $kunjungan->biaya_konsultasi,
                'subtotal' => $kunjungan->biaya_konsultasi,
                'tanggal' => $tanggal->toIso8601String(), // Changed format
            ];
        }

        // 2. Ambil data RawatInap yang belum dibayar (is_paid = 0)
        $rawatInaps = RawatInap::where('pasien_id', $pasien_id)
            ->where('is_paid', 0)
            ->with('ruangan')
            ->get();

        foreach ($rawatInaps as $rawatInap) {
            $tanggal = $rawatInap->tanggal_masuk instanceof Carbon 
                ? $rawatInap->tanggal_masuk 
                : Carbon::parse($rawatInap->tanggal_masuk);

            $unpaidCosts[] = [
                'id' => $rawatInap->id,
                'type' => 'rawat_inap',
                'type_label' => 'Rawat Inap',
                'description' => 'Rawat inap di Ruangan ' . ($rawatInap->ruangan->nama ?? ''),
                'jumlah' => 1,
                'harga_satuan' => $rawatInap->biaya_inap,
                'subtotal' => $rawatInap->biaya_inap,
                'tanggal' => $tanggal->toIso8601String(), // Changed format
            ];
        }

        // 3. Ambil data ResepObat yang belum dibayar (is_paid = 0)
        $resepObats = ResepObat::where('pasien_id', $pasien_id)
            ->where('is_paid', 0)
            ->with('obat')
            ->get();

        foreach ($resepObats as $resepObat) {
            // Hitung harga satuan obat (harga_jual jika ada, atau 0)
            $obat = $resepObat->obat;
            $hargaSatuan = $obat && isset($obat->harga_jual) ? $obat->harga_jual : 0;
            $subtotal = $resepObat->jumlah * $hargaSatuan;

            $tanggal = $resepObat->created_at instanceof Carbon 
                ? $resepObat->created_at 
                : Carbon::parse($resepObat->created_at);

            $unpaidCosts[] = [
                'id' => $resepObat->id,
                'type' => 'resep_obat',
                'type_label' => 'Obat',
                'description' => 'Obat: ' . ($obat->nama_obat ?? 'N/A'),
                'jumlah' => $resepObat->jumlah,
                'harga_satuan' => $hargaSatuan,
                'subtotal' => $subtotal,
                'tanggal' => $tanggal->toIso8601String(), // Changed format
            ];
        }

        // 4. Ambil data Radiologi yang belum dibayar (is_paid = 0)
        $radiologis = Radiologi::where('pasien_id', $pasien_id)
            ->where('is_paid', 0)
            ->get();

        foreach ($radiologis as $radiologi) {
            $tanggal = $radiologi->tanggal instanceof Carbon 
                ? $radiologi->tanggal 
                : Carbon::parse($radiologi->tanggal);

            $unpaidCosts[] = [
                'id' => $radiologi->id,
                'type' => 'radiologi',
                'type_label' => 'Radiologi',
                'description' => 'Pemeriksaan Radiologi: ' . $radiologi->jenis_radiologi,
                'jumlah' => 1,
                'harga_satuan' => $radiologi->biaya_radiologi,
                'subtotal' => $radiologi->biaya_radiologi,
                'tanggal' => $tanggal->toIso8601String(), // Changed format
            ];
        }

        // 5. Ambil data LabPemeriksaan yang belum dibayar (is_paid = 0)
        $labPemeriksaans = LabPemeriksaan::where('pasien_id', $pasien_id)
            ->where('is_paid', 0)
            ->get();

        foreach ($labPemeriksaans as $labPemeriksaan) {
            $tanggal = $labPemeriksaan->tanggal instanceof Carbon 
                ? $labPemeriksaan->tanggal 
                : Carbon::parse($labPemeriksaan->tanggal);

            $unpaidCosts[] = [
                'id' => $labPemeriksaan->id,
                'type' => 'lab_pemeriksaan',
                'type_label' => 'Lab',
                'description' => 'Pemeriksaan Lab: ' . $labPemeriksaan->jenis_pemeriksaan,
                'jumlah' => 1,
                'harga_satuan' => $labPemeriksaan->biaya_lab,
                'subtotal' => $labPemeriksaan->biaya_lab,
                'tanggal' => $tanggal->toIso8601String(), // Changed format
            ];
        }

        return $unpaidCosts;
    }

    public function all($_, array $args)
    {
        $query = DetailPembayaranPasien::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('kunjungan_id', 'like', "%$search%")
                    ->where('rawat_inap_id', 'like', "%$search%")
                    ->where('resep_id', 'like', "%$search%")
                    ->where('radiologi_id', 'like', "%$search%")
                    ->where('lab_id', 'like', "%$search%")
                    ->orWhere('jumlah', 'like', "%$search%")
                    ->orWhere('harga_satuan', 'like', "%$search%")
                    ->orWhere('subtotal', 'like', "%$search%");
            })
                ->orWhereHas('pembayaranPasien.pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                })
                ->orWhereHas('resep.obat', function ($q) use ($search) {
                    $q->where('nama_obat', 'like', "%$search%");
                })
                ->orWhereHas('kunjungan.pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                })
            ;
        }

        $perPage = $args['first'] ?? 10;
        $page = $args['page'] ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'paginatorInfo' => [
                'hasMorePages' => $paginator->hasMorePages(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    public function allArchive($_, array $args)
    {
        $query = DetailPembayaranPasien::onlyTrashed();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('kunjungan_id', 'like', "%$search%")
                    ->where('rawat_inap_id', 'like', "%$search%")
                    ->where('resep_id', 'like', "%$search%")
                    ->where('radiologi_id', 'like', "%$search%")
                    ->where('lab_id', 'like', "%$search%")
                    ->orWhere('jumlah', 'like', "%$search%")
                    ->orWhere('harga_satuan', 'like', "%$search%")
                    ->orWhere('subtotal', 'like', "%$search%");
            })
                ->orWhereHas('pembayaranPasien.pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                })
                ->orWhereHas('resep.obat', function ($q) use ($search) {
                    $q->where('nama_obat', 'like', "%$search%");
                })
                ->orWhereHas('kunjungan.pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                })
            ;
        }

        $perPage = $args['first'] ?? 10;
        $page = $args['page'] ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'paginatorInfo' => [
                'hasMorePages' => $paginator->hasMorePages(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }
}