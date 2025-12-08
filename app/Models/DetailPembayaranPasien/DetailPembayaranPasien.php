<?php

namespace App\Models\DetailPembayaranPasien;

use App\Models\Obat\Obat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PembayaranPasien\PembayaranPasien;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPembayaranPasien extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'detail_pembayaran_pasien';
    protected $primaryKey = 'id';
    protected $fillable = ['pembayaran_id', 'tipe_biaya', 'referensi_id', 'jumlah', 'harga_satuan', 'subtotal'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pembayaranPasien()
    {
        return $this->belongsTo(PembayaranPasien::class, 'pembayaran_id');
    }

}
