<?php

namespace App\Models\LabPemeriksaan;

use App\Models\Pasien\Pasien;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabPemeriksaan extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'lab_pemeriksaan';
    protected $primaryKey = 'id';
    protected $fillable = ['pasien_id', 'tenaga_medis_id', 'jenis_pemeriksaan', 'hasil', 'tanggal', 'biaya_lab'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function tenagaMedis()
    {
        return $this->belongsTo(TenagaMedis::class, 'tenaga_medis_id');
    }
}
