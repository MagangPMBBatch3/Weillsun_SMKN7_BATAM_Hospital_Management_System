<?php

namespace App\Models\LogAktivitas;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogAktivitas extends Model
{
        use SoftDeletes;

    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'aktivitas', 'waktu'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
