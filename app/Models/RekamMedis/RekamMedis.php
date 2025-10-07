<?php

namespace App\Models\RekamMedis;

use App\Models\Pasien\Pasien;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekamMedis extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'rekam_medis';
    protected $primaryKey = 'id';
    protected $fillable = ['pasien_id','tenaga_medis_id','tanggal','diagnosis','tindakan'];

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
