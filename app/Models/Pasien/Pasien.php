<?php

namespace App\Models\Pasien;

use App\Models\Kunjungan\Kunjungan;
use App\Models\Radiologi\Radiologi;
use App\Models\RawatInap\RawatInap;
use App\Models\ResepObat\ResepObat;
use App\Models\RekamMedis\RekamMedis;
use Illuminate\Database\Eloquent\Model;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PembayaranPasien\PembayaranPasien;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasien extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pasien';
    protected $primaryKey = 'id';
    protected $fillable = ['nama', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'telepon'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'pasien_id');
    }

    public function labPemeriksaan()
    {
        return $this->hasMany(LabPemeriksaan::class, 'pasien_id');
    }

    public function radiologi()
    {
        return $this->hasMany(Radiologi::class, 'pasien_id');
    }

    public function rawatInap()
    {
        return $this->hasOne(RawatInap::class, 'pasien_id');
    }

    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'pasien_id');
    }

    public function resepObat()
    {
        return $this->hasMany(ResepObat::class, 'pasien_id');
    }

    public function pembayaranPasien()
    {
        return $this->hasMany(PembayaranPasien::class, 'pasien_id');
    }
}
