<?php

namespace App\Models\RawatInap;

use App\Models\Pasien\Pasien;
use App\Models\Ruangan\Ruangan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RawatInap extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'rawat_inap';
    protected $primaryKey = 'id';
    protected $fillable = ['pasien_id','ruangan_id','tanggal_masuk','tanggal_keluar','status','biaya_inap'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}
