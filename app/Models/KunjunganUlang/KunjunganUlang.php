<?php

namespace App\Models\KunjunganUlang;

use Carbon\Carbon;
use App\Models\Poli\Poli;
use App\Models\Kunjungan\Kunjungan;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\JadwalTenagaMedis\JadwalTenagaMedis;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KunjunganUlang extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kunjungan_ulang';
    protected $primaryKey = 'id';
    protected $fillable = ['kunjungan_id', 'tenaga_medis_id', 'poli_id', 'tanggal_ulang', 'jam_ulang', 'catatan', 'status'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            self::validateJamUlang($model);
        });

        static::updating(function ($model) {
            self::validateJamUlang($model);
        });
    }

    public static function validateJamUlang($model)
    {
        if (!$model->tanggal_ulang || !$model->jam_ulang || !$model->tenaga_medis_id) {
            return;
        }

        // Get day of week from tanggal_ulang (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
        $date = Carbon::parse($model->tanggal_ulang);
        $hari = $date->dayOfWeek; // 0-6
        // Convert to 1-7 (Monday = 1, Sunday = 7)
        $hari = $hari === 0 ? 7 : $hari;

        // Get jadwal for this doctor on this day
        $jadwal = JadwalTenagaMedis::where('tenaga_medis_id', $model->tenaga_medis_id)
            ->where('hari', $hari)
            ->first();

        if (!$jadwal) {
            throw new \Exception("Doctor has no schedule on this day");
        }

        // Check if jam_ulang is within the doctor's schedule
        $jamUlang = $model->jam_ulang;
        $jamMulai = $jadwal->jam_mulai;
        $jamSelesai = $jadwal->jam_selesai;
        
        if ($jamMulai <= $jamSelesai) {
           
            if ($jamUlang < $jamMulai || $jamUlang > $jamSelesai) {
                throw new \Exception("Follow-up time must be between {$jamMulai} and {$jamSelesai}");
            }
        } else {
            if ($jamUlang < $jamMulai && $jamUlang > $jamSelesai) {
                throw new \Exception("Follow-up time must be between {$jamMulai} and {$jamSelesai} (overnight shift)");
            }
        }
    }

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }

    public function tenagaMedis()
    {
        return $this->belongsTo(TenagaMedis::class, 'tenaga_medis_id');
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }
}