<?php

namespace App\Models\Kunjungan;

use App\Models\Poli\Poli;
use App\Models\Pasien\Pasien;
use Illuminate\Database\Eloquent\Model;
use App\Models\KunjunganUlang\KunjunganUlang;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kunjungan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kunjungan';
    protected $primaryKey = 'id';
    protected $fillable = ['pasien_id', 'poli_id', 'tanggal_kunjungan', 'keluhan', 'biaya_konsultasi'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'poli_id');
    }

    public function kunjunganUlang()
    {
        return $this->hasMany(KunjunganUlang::class, 'kunjungan_id');
    }
}
