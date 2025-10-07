<?php

namespace App\Models\PembelianObat;

use App\Models\Supplier\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PembayaranSupplier\PembayaranSupplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DetailPembelianObat\DetailPembelianObat;

class PembelianObat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pembelian_obat';
    protected $primaryKey = 'id';
    protected $fillable = ['supplier_id','tanggal','total_biaya','status'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPembelianObat::class, 'pembelian_id');
    }

    public function pembayaranSupplier()
    {
        return $this->hasMany(PembayaranSupplier::class, 'pembelian_id');
    }
}
