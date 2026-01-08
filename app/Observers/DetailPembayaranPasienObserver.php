<?php

namespace App\Observers;

use App\Models\RawatInap\RawatInap;
use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;

// use App\Models\DetailPembayaranPasien;

class DetailPembayaranPasienObserver
{
    /**
     * Handle the DetailPembayaranPasien "created" event.
     */
    public function created(DetailPembayaranPasien $detail): void
    {
        // 1. Cek tipe biaya
        if ($detail->tipe_biaya !== 'rawat_inap') {
            return;
        }

        // 2. Ambil rawat inap berdasarkan referensi_id
        $rawatInap = RawatInap::where('id', $detail->referensi_id)
            ->where('status', 'Aktif')
            ->first();

        // 3. Jika ditemukan, ubah status jadi Pulang
        if ($rawatInap) {
            $rawatInap->update([
                'status' => 'Pulang',
            ]);
        }
    }

    /**
     * Handle the DetailPembayaranPasien "updated" event.
     */
    public function updated(DetailPembayaranPasien $detailPembayaranPasien): void
    {
        //
    }

    /**
     * Handle the DetailPembayaranPasien "deleted" event.
     */
    public function deleted(DetailPembayaranPasien $detailPembayaranPasien): void
    {
        //
    }

    /**
     * Handle the DetailPembayaranPasien "restored" event.
     */
    public function restored(DetailPembayaranPasien $detailPembayaranPasien): void
    {
        //
    }

    /**
     * Handle the DetailPembayaranPasien "force deleted" event.
     */
    public function forceDeleted(DetailPembayaranPasien $detailPembayaranPasien): void
    {
        //
    }
}
