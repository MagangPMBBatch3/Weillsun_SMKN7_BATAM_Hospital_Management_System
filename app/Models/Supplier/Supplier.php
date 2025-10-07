<?php

namespace App\Models\Supplier;

use Illuminate\Database\Eloquent\Model;
use App\Models\PembelianObat\PembelianObat;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'supplier';
    protected $primaryKey = 'id';
    protected $fillable = ['nama_supplier', 'alamat', 'telepon', 'email'];

    public function pembelianObat()
    {
        return $this->hasMany(PembelianObat::class, 'supplier_id');
    }
}
