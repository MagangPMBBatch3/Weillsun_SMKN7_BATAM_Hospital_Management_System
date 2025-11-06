<?php

namespace App\Models\Obat;

use App\Models\ResepObat\ResepObat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DetailPembelianObat\DetailPembelianObat;

class Obat extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'obat';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_obat', 'jenis_obat', 'stok', 'harga', 'markup_persen', 'harga_jual'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelianObat::class, 'obat_id');
    }

    public function resepObat()
    {
        return $this->hasMany(ResepObat::class, 'obat_id');
    }
}
