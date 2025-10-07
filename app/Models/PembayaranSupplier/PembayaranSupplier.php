<?php

namespace App\Models\PembayaranSupplier;

use Illuminate\Database\Eloquent\Model;
use App\Models\PembelianObat\PembelianObat;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembayaranSupplier extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pembayaran_supplier';
    protected $primaryKey = 'id';
    protected $fillable = ['pembelian_id','jumlah_bayar','metode_bayar','tanggal_bayar'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pembelianObat()
    {
        return $this->belongsTo(PembelianObat::class, 'pembelian_id');
    }
}
