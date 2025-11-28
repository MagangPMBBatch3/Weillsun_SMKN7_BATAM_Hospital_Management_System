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

    public function markAsPaid($_, array $args): bool
    {
        $detailId = $args['detail_id'] ?? null;
        $tipeBiaya = $args['tipe_biaya'] ?? null;
        $referensiId = $args['referensi_id'] ?? null;

        if (!$detailId || !$tipeBiaya || !$referensiId) {
            return false;
        }
// 
        try {
            switch ($tipeBiaya) {
                case 'konsultasi':
                    Kunjungan::find($referensiId)?->update(['is_paid' => 1]);
                    break;
                case 'obat':
                    ResepObat::find($referensiId)?->update(['is_paid' => 1]);
                    break;
                case 'lab':
                    LabPemeriksaan::find($referensiId)?->update(['is_paid' => 1]);
                    break;
                case 'radiologi':
                    Radiologi::find($referensiId)?->update(['is_paid' => 1]);
                    break;
                case 'rawat_inap':
                    RawatInap::find($referensiId)?->update(['is_paid' => 1]);
                    break;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
