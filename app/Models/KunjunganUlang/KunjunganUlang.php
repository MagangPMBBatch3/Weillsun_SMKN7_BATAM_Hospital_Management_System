<?php

namespace App\Models\KunjunganUlang;

use App\Models\Kunjungan\Kunjungan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KunjunganUlang extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kunjungan_ulang';
    protected $primaryKey = 'id';
    protected $fillable = ['kunjungan_id', 'tanggal_ulang', 'jam_ulang', 'catatan'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }
}
