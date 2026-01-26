<?php

namespace App\GraphQL\DetailPembayaranPasien\Mutations;

use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;
use App\Models\Kunjungan\Kunjungan;
use App\Models\ResepObat\ResepObat;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use App\Models\Radiologi\Radiologi;
use App\Models\RawatInap\RawatInap;

class DetailPembayaranPasienMutation
{
    public function create($_, array $args): ?DetailPembayaranPasien
    {
        $input = $args['input'];

        // Tentukan field yang akan disimpan berdasarkan input
        $data = [
            'pembayaran_id' => $input['pembayaran_id'] ?? null,
            'jumlah' => $input['jumlah'] ?? 1,
            'harga_satuan' => $input['harga_satuan'] ?? 0,
            'subtotal' => $input['subtotal'] ?? 0,
        ];

        // Menentukan tipe biaya berdasarkan input
        // Jika ada kunjungan_id, gunakan itu sebagai referensi
        if (!empty($input['kunjungan_id'])) {
            $data['kunjungan_id'] = $input['kunjungan_id'];
            // Update is_paid pada kunjungan
            Kunjungan::find($input['kunjungan_id'])?->update(['is_paid' => 1]);
        }

        if (!empty($input['rawat_inap_id'])) {
            $data['rawat_inap_id'] = $input['rawat_inap_id'];
            // Update is_paid pada rawat_inap
            RawatInap::find($input['rawat_inap_id'])?->update(['is_paid' => 1]);
        }

        if (!empty($input['resep_id'])) {
            $data['resep_id'] = $input['resep_id'];
            // Update is_paid pada resep_obat
            ResepObat::find($input['resep_id'])?->update(['is_paid' => 1]);
        }

        if (!empty($input['radiologi_id'])) {
            $data['radiologi_id'] = $input['radiologi_id'];
            // Update is_paid pada radiologi
            Radiologi::find($input['radiologi_id'])?->update(['is_paid' => 1]);
        }

        if (!empty($input['lab_id'])) {
            $data['lab_id'] = $input['lab_id'];
            // Update is_paid pada lab_pemeriksaan
            LabPemeriksaan::find($input['lab_id'])?->update(['is_paid' => 1]);
        }

        return DetailPembayaranPasien::create($data);
    }

    public function delete($_, array $args): ?DetailPembayaranPasien
    {
        $detailPembayaran = DetailPembayaranPasien::find($args['id']);

        if ($detailPembayaran) {
            // Update is_paid kembali ke 0 ketika detail pembayaran didelete
            if ($detailPembayaran->kunjungan_id) {
                Kunjungan::find($detailPembayaran->kunjungan_id)?->update(['is_paid' => 0]);
            }
            if ($detailPembayaran->rawat_inap_id) {
                RawatInap::find($detailPembayaran->rawat_inap_id)?->update(['is_paid' => 0]);
            }
            if ($detailPembayaran->resep_id) {
                ResepObat::find($detailPembayaran->resep_id)?->update(['is_paid' => 0]);
            }
            if ($detailPembayaran->radiologi_id) {
                Radiologi::find($detailPembayaran->radiologi_id)?->update(['is_paid' => 0]);
            }
            if ($detailPembayaran->lab_id) {
                LabPemeriksaan::find($detailPembayaran->lab_id)?->update(['is_paid' => 0]);
            }

            $detailPembayaran->delete();
            return $detailPembayaran;
        }

        return null;
    }

    public function restore($_, array $args): ?DetailPembayaranPasien
    {
        return DetailPembayaranPasien::withTrashed()->find($args['id'])?->restore()
            ? DetailPembayaranPasien::find($args['id'])
            : null;
    }

    public function forceDelete($_, array $args): ?DetailPembayaranPasien
    {
        $DetailPembayaranPasien = DetailPembayaranPasien::withTrashed()->find($args['id']);
        if ($DetailPembayaranPasien) {
            $DetailPembayaranPasien->forceDelete();
            return $DetailPembayaranPasien;
        }
        return null;
    }
}
