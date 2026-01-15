<?php

namespace App\Models\KunjunganUlang;

use App\Models\Poli\Poli;
use App\Models\Kunjungan\Kunjungan;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
