<?php

namespace App\Models\LogRuangan;

use App\Models\Pasien\Pasien;
use App\Models\Ruangan\Ruangan;
use App\Models\RawatInap\RawatInap;
use Illuminate\Database\Eloquent\Model;

class LogRuangan extends Model
{
    protected $table = 'log_ruangan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ruangan_id',
        'rawat_inap_id',
        'pasien_id',
        'status_sebelum',
        'status_sesudah',
        'aksi',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function rawatInap()
    {
        return $this->belongsTo(RawatInap::class, 'rawat_inap_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }
}
