<?php

namespace App\Models\ResepObat;

use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;
use App\Models\Obat\Obat;
use App\Models\Pasien\Pasien;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResepObat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'resep_obat';
    protected $fillable = ['pasien_id','tenaga_medis_id','obat_id','jumlah','aturan_pakai'];

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

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    public function detailPasien()
    {
        return $this->hasMany(DetailPembayaranPasien::class, 'resep_id');
    }
}
