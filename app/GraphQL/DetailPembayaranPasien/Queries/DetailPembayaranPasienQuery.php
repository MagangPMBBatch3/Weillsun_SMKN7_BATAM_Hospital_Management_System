<?php

namespace App\GraphQL\DetailPembayaranPasien\Queries;

use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;
use App\Models\Kunjungan\Kunjungan;
use App\Models\ResepObat\ResepObat;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use App\Models\Radiologi\Radiologi;
use App\Models\RawatInap\RawatInap;

class DetailPembayaranPasienQuery
{
    public function all($_, array $args)
    {
        $query = DetailPembayaranPasien::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tipe_biaya', 'like', "%$search%")
                    ->orWhere('jumlah', 'like', "%$search%")
                    ->orWhere('harga_satuan', 'like', "%$search%")
                    ->orWhere('subtotal', 'like', "%$search%");
            })
                ->orWhereHas('pembayaranPasien.pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                });
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
                $q->where('tipe_biaya', 'like', "%$search%")
                    ->orWhere('jumlah', 'like', "%$search%")
                    ->orWhere('harga_satuan', 'like', "%$search%")
                    ->orWhere('subtotal', 'like', "%$search%");
            })
                ->orWhereHas('pembayaranPasien.pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                });
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

    public function getUnpaidCostsByPasien($_, array $args)
    {
        $pasienId = $args['pasien_id'] ?? null;

        if (!$pasienId) {
            return [];
        }

        $costs = [];

        // Ambil biaya konsultasi dari Kunjungan yang belum dibayar
        $kunjungan = Kunjungan::where('pasien_id', $pasienId)
            ->where('is_paid', 0)
            ->get();

        foreach ($kunjungan as $k) {
            $costs[] = [
                'type' => 'konsultasi',
                'referensi_id' => $k->id,
                'jumlah' => 1,
                'harga_satuan' => $k->biaya_konsultasi,
                'subtotal' => $k->biaya_konsultasi,
                'label' => 'Konsultasi - ' . ($k->poli?->nama ?? 'N/A')
            ];
        }

        // Ambil biaya obat dari ResepObat yang belum dibayar
        $resepObat = ResepObat::where('pasien_id', $pasienId)
            ->where('is_paid', 0)
            ->with('obat')
            ->get();

        foreach ($resepObat as $r) {
            $harga = $r->obat?->harga ?? 0;
            $costs[] = [
                'type' => 'obat',
                'referensi_id' => $r->id,
                'jumlah' => $r->jumlah,
                'harga_satuan' => $harga,
                'subtotal' => $r->jumlah * $harga,
                'label' => 'Resep Obat - ' . ($r->obat?->nama_obat ?? 'N/A')
            ];
        }

        // Ambil biaya lab yang belum dibayar
        $labPemeriksaan = LabPemeriksaan::where('pasien_id', $pasienId)
            ->where('is_paid', 0)
            ->get();

        foreach ($labPemeriksaan as $l) {
            $costs[] = [
                'type' => 'lab',
                'referensi_id' => $l->id,
                'jumlah' => 1,
                'harga_satuan' => $l->biaya_lab,
                'subtotal' => $l->biaya_lab,
                'label' => 'Lab - ' . $l->jenis_pemeriksaan
            ];
        }

        // Ambil biaya radiologi yang belum dibayar
        $radiologi = Radiologi::where('pasien_id', $pasienId)
            ->where('is_paid', 0)
            ->get();

        foreach ($radiologi as $r) {
            $costs[] = [
                'type' => 'radiologi',
                'referensi_id' => $r->id,
                'jumlah' => 1,
                'harga_satuan' => $r->biaya_radiologi,
                'subtotal' => $r->biaya_radiologi,
                'label' => 'Radiologi - ' . $r->jenis_radiologi
            ];
        }

        // Ambil biaya rawat inap yang belum dibayar
        $rawatInap = RawatInap::where('pasien_id', $pasienId)
            ->where('is_paid', 0)
            ->get();

        foreach ($rawatInap as $r) {
            $costs[] = [
                'type' => 'rawat_inap',
                'referensi_id' => $r->id,
                'jumlah' => 1,
                'harga_satuan' => $r->biaya_inap,
                'subtotal' => $r->biaya_inap,
                'label' => 'Rawat Inap'
            ];
        }

        return $costs;
    }
}
