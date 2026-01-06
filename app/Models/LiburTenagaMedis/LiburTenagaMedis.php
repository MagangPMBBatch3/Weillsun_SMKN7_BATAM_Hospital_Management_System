<?php

namespace App\Models\LiburTenagaMedis;

use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiburTenagaMedis extends Model
{
    use SoftDeletes;
    protected $table = 'libur_tenaga_medis';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tenaga_medis_id',
        'tanggal',
        'jenis',
        'keterangan',
    ];

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
