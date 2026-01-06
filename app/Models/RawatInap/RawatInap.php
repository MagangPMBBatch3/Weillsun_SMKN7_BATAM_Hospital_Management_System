<?php

namespace App\Models\RawatInap;

use App\Models\Pasien\Pasien;
use App\Models\Ruangan\Ruangan;
use App\Models\LogRuangan\LogRuangan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;

class RawatInap extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'rawat_inap';
    protected $primaryKey = 'id';
    protected $fillable = ['pasien_id','ruangan_id','status','biaya_inap'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'tanggal_masuk' => 'datetime',
        'tanggal_keluar' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function detailPasien()
    {
        return $this->hasMany(DetailPembayaranPasien::class, 'inap_id');
    }

    public function logRuangan()
{
    return $this->hasMany(LogRuangan::class, 'rawat_inap_id');
}

}
