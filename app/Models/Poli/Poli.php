<?php

namespace App\Models\Poli;

use App\Models\Kunjungan\Kunjungan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poli extends Model
{
        use SoftDeletes;

    protected $table = 'poli';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_poli', 'deskripsi'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'poli_id');
    }
}
