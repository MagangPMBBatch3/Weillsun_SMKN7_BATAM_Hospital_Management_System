<?php

namespace App\Models\PembayaranPasien;

use App\Models\Pasien\Pasien;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;

class PembayaranPasien extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pembayaran_pasien';
    protected $fillable = ['pasien_id', 'total_biaya', 'metode_bayar', 'tanggal_bayar'];
    protected $primaryKey = 'id';
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPembayaranPasien::class, 'pembayaran_id');
    }
}
