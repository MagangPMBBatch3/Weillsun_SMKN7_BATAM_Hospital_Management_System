<?php

namespace App\Models\Radiologi;

use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;
use App\Models\Pasien\Pasien;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Radiologi extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'radiologi';
    protected $primaryKey = 'id';

    protected $fillable = ['pasien_id','tenaga_medis_id','jenis_radiologi','hasil','tanggal','biaya_radiologi'];

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

    public function detailPasien()
    {
        return $this->hasMany(DetailPembayaranPasien::class, 'radiologi_id');
    }
}
