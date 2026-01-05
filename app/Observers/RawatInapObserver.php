<?php

namespace App\Observers;

use App\Models\RawatInap\RawatInap;
use App\Models\LogRuangan\LogRuangan;

class RawatInapObserver
{
    /**
     * Handle the RawatInap "created" event.
     */
    public function created(RawatInap $rawatInap): void
    {
        // Catat log saat rawat inap baru dibuat
        $ruangan = $rawatInap->ruangan;
        
        if ($ruangan) {
            LogRuangan::create([
                'ruangan_id' => $ruangan->id,
                'rawat_inap_id' => $rawatInap->id,
                'pasien_id' => $rawatInap->pasien_id,
                'status_sebelum' => 'tersedia',
                'status_sesudah' => 'tidak_tersedia',
                'aksi' => 'MASUK',
                'waktu' => now(),
            ]);

            // Update status ruangan menjadi tidak tersedia
            $ruangan->update(['status' => 'tidak_tersedia']);
        }
    }

    /**
     * Handle the RawatInap "deleted" event (softdelete).
     */
    public function deleted(RawatInap $rawatInap): void
    {
        // Hanya proses softdelete, bukan force delete
        if (!$rawatInap->isForceDeleting()) {
            // Get the related ruangan (gunakan withTrashed jika ingin ambil yang sudah deleted)
            $ruangan = $rawatInap->ruangan;
            
            if ($ruangan) {
                // Jika status rawat inap bukan 'Pulang', ubah status ruangan menjadi 'tersedia'
                // Jangan buat log untuk softdelete, hanya ubah status ruangan
                if ($rawatInap->status !== 'Pulang') {
                    $ruangan->update(['status' => 'tersedia']);
                }
            }
        }
    }

    /**
     * Handle the RawatInap "restored" event.
     */
    public function restored(RawatInap $rawatInap): void
    {
        // Get the related ruangan
        $ruangan = $rawatInap->ruangan;
        
        if ($ruangan) {
            // Jika status rawat inap bukan 'Pulang', kembalikan status ruangan ke 'tidak_tersedia'
            if ($rawatInap->status !== 'Pulang') {
                $statusSebelum = $ruangan->status;
                $ruangan->update(['status' => 'tidak_tersedia']);
                
                // Catat di log_ruangan untuk restore (perubahan dari tersedia ke tidak tersedia)
                // Hanya jika ada perubahan status
                if ($statusSebelum === 'tersedia') {
                    LogRuangan::create([
                        'ruangan_id' => $ruangan->id,
                        'rawat_inap_id' => $rawatInap->id,
                        'pasien_id' => $rawatInap->pasien_id,
                        'status_sebelum' => 'tersedia',
                        'status_sesudah' => 'tidak_tersedia',
                        'aksi' => 'RESTORE',
                        'waktu' => now(),
                    ]);
                }
            }
            // Jika status adalah 'Pulang', jangan ubah status ruangan (tetap tersedia)
            // Tidak perlu log karena status sudah sesuai
        }
    }

    /**
     * Handle the RawatInap "force deleted" event.
     */
    public function forceDeleted(RawatInap $rawatInap): void
    {
        // Tidak ada aksi khusus untuk permanent delete
    }
}
