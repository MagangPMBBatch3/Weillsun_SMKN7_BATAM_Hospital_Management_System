<?php

namespace App\Models\DetailPembelianObat;

use App\Models\Obat\Obat;
use Illuminate\Database\Eloquent\Model;
use App\Models\PembelianObat\PembelianObat;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPembelianObat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'detail_pembelian_obat';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pembelian_id',
        'obat_id',
        'jumlah',
        'harga_satuan',
        'harga_beli',
        'subtotal'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pembelianObat()
    {
        return $this->belongsTo(PembelianObat::class, 'pembelian_id');
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
}
