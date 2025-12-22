<?php

namespace App\Models\Ruangan;

use App\Models\RawatInap\RawatInap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruangan extends Model
{
        use SoftDeletes;

    protected $table = 'ruangan';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_ruangan', 'kapasitas', 'tarif_per_hari', 'status'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function rawatInap()
    {
        return $this->hasMany(RawatInap::class, 'ruangan_id');
    }
}
