<?php

namespace App\Models\TenagaMedis;

use App\Models\Radiologi\Radiologi;
use App\Models\ResepObat\ResepObat;
use App\Models\RekamMedis\RekamMedis;
use Illuminate\Database\Eloquent\Model;
use App\Models\UsersProfile\UsersProfile;
use App\Models\LabPemeriksaan\LabPemeriksaan;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\JadwalTenagaMedis\JadwalTenagaMedis;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TenagaMedis extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tenaga_medis';
    protected $primaryKey = 'id';
    protected $fillable = ['profile_id','spesialisasi','no_str'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalTenagaMedis::class, 'tenaga_medis_id');
    }

    public function labPemeriksaan()
    {
        return $this->hasMany(LabPemeriksaan::class, 'tenaga_medis_id');
    }

    public function radiologi()
    {
        return $this->hasMany(Radiologi::class, 'tenaga_medis_id');
    }

    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'tenaga_medis_id');
    }

    public function resepObat()
    {
        return $this->hasMany(ResepObat::class, 'tenaga_medis_id');
    }
}
