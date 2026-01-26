<?php

namespace App\Models\DetailPembayaranPasien;

use App\Models\Obat\Obat;
use App\Models\Kunjungan\Kunjungan;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use App\Models\Radiologi\Radiologi;
use App\Models\RawatInap\RawatInap;
use App\Models\ResepObat\ResepObat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PembayaranPasien\PembayaranPasien;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPembayaranPasien extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'detail_pembayaran_pasien';
    protected $primaryKey = 'id';
    protected $fillable = ['pembayaran_id', 'kunjungan_id', 'rawat_inap_id', 'resep_id', 'radiologi_id', 'lab_id', 'jumlah', 'harga_satuan', 'subtotal'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pembayaranPasien()
    {
        return $this->belongsTo(PembayaranPasien::class, 'pembayaran_id');
    }

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }

    public function rawatInap()
    {
        return $this->belongsTo(RawatInap::class, 'rawat_inap_id');
    }

    public function resep()
    {
        return $this->belongsTo(ResepObat::class, 'resep_id');
    }

    public function radiologi()
    {
        return $this->belongsTo(Radiologi::class, 'radiologi_id');
    }

    public function lab()
    {
        return $this->belongsTo(LabPemeriksaan::class, 'lab_id');
    }

}
