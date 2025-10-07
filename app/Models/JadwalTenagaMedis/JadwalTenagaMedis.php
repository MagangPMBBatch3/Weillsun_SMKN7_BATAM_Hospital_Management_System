<?php

namespace App\Models\JadwalTenagaMedis;

use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalTenagaMedis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal_tenaga_medis';
    protected $primaryKey = 'id';
    protected $fillable = ['tenaga_medis_id','tanggal','jam_mulai','jam_selesai'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function tenagaMedis()
    {
        return $this->belongsTo(TenagaMedis::class, 'tenaga_medis_id');
    }
}
