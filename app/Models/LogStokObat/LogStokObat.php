<?php

namespace App\Models\LogStokObat;

use App\Models\Obat\Obat;
use Illuminate\Database\Eloquent\Model;
use App\Models\PembelianObat\PembelianObat;

class LogStokObat extends Model
{
    protected $table = 'log_stok_obat';
    protected $primaryKey = 'id';

    protected $fillable = [
        'obat_id',
        'jenis',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'referensi_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    public function pembelianObat()
    {
        return $this->belongsTo(PembelianObat::class, 'referensi_id');
    }
}
