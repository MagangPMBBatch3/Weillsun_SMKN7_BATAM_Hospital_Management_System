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
        // Ketika menggunakan @spread, input fields langsung ada di $args
        $pembayaran_id = $args['pembayaran_id'] ?? null;
        $tipe_biaya = $args['tipe_biaya'] ?? null;
        $referensi_id = $args['referensi_id'] ?? null;
        $jumlah = $args['jumlah'] ?? null;
        $harga_satuan = $args['harga_satuan'] ?? null;
        $subtotal = $args['subtotal'] ?? null;

        if (!$pembayaran_id || !$tipe_biaya || !$jumlah || !$harga_satuan || !$subtotal) {
            throw new \Exception('Missing required fields');
        }

        $input = [
            'pembayaran_id' => $pembayaran_id,
            'tipe_biaya' => $tipe_biaya,
            'referensi_id' => $referensi_id,
            'jumlah' => $jumlah,
            'harga_satuan' => $harga_satuan,
            'subtotal' => $subtotal,
        ];

        // Buat DetailPembayaranPasien
        $detail = DetailPembayaranPasien::create($input);

        // Update is_paid = 1 berdasarkan tipe_biaya
        if ($detail) {
            $this->updateIsPaidStatus($tipe_biaya, $referensi_id);
        }

        return $detail;
    }

    private function updateIsPaidStatus(string $tipeBiaya, $referensiId): void
    {
        if (!$referensiId) {
            return;
        }

        switch ($tipeBiaya) {
            case 'konsultasi':
                Kunjungan::where('id', $referensiId)->update(['is_paid' => 1]);
                break;
            case 'obat':
                ResepObat::where('id', $referensiId)->update(['is_paid' => 1]);
                break;
            case 'lab':
                LabPemeriksaan::where('id', $referensiId)->update(['is_paid' => 1]);
                break;
            case 'radiologi':
                Radiologi::where('id', $referensiId)->update(['is_paid' => 1]);
                break;
            case 'rawat_inap':
                RawatInap::where('id', $referensiId)->update(['is_paid' => 1]);
                break;
        }
    }

    private function revertIsPaidStatus(string $tipeBiaya, $referensiId): void
    {
        if (!$referensiId) {
            return;
        }

        switch ($tipeBiaya) {
            case 'konsultasi':
                Kunjungan::where('id', $referensiId)->update(['is_paid' => 0]);
                break;
            case 'obat':
                ResepObat::where('id', $referensiId)->update(['is_paid' => 0]);
                break;
            case 'lab':
                LabPemeriksaan::where('id', $referensiId)->update(['is_paid' => 0]);
                break;
            case 'radiologi':
                Radiologi::where('id', $referensiId)->update(['is_paid' => 0]);
                break;
            case 'rawat_inap':
                RawatInap::where('id', $referensiId)->update(['is_paid' => 0]);
                break;
        }
    }

    public function delete($_, array $args): ?DetailPembayaranPasien
    {
        $id = $args['id'] ?? null;
        if (!$id) {
            throw new \Exception('ID is required');
        }

        $detail = DetailPembayaranPasien::find($id);

        if ($detail) {
            // Revert is_paid status sebelum delete
            $this->revertIsPaidStatus($detail->tipe_biaya, $detail->referensi_id);

            // Soft delete
            $detail->delete();
        }

        return $detail;
    }

    public function restore($_, array $args): ?DetailPembayaranPasien
    {
        $id = $args['id'] ?? null;
        if (!$id) {
            throw new \Exception('ID is required');
        }

        $detail = DetailPembayaranPasien::withTrashed()->find($id);

        if ($detail) {
            // Restore the record
            $detail->restore();

            // Update is_paid status kembali menjadi 1
            $this->updateIsPaidStatus($detail->tipe_biaya, $detail->referensi_id);
        }

        return $detail ? DetailPembayaranPasien::find($id) : null;
    }

    public function forceDelete($_, array $args): ?DetailPembayaranPasien
    {
        $id = $args['id'] ?? null;
        if (!$id) {
            throw new \Exception('ID is required');
        }

        $detail = DetailPembayaranPasien::withTrashed()->find($id);

        if ($detail) {
            // Revert is_paid status sebelum permanent delete
            $this->revertIsPaidStatus($detail->tipe_biaya, $detail->referensi_id);

            // Force delete
            $detail->forceDelete();
        }

        return $detail;
    }
}
