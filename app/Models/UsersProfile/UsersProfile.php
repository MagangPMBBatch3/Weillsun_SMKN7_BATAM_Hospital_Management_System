<?php

namespace App\Models\UsersProfile;

use App\Models\User;
use App\Models\TenagaMedis\TenagaMedis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsersProfile extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'users_profile';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id','nama','email','telepon','alamat','foto'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tenagaMedis()
    {
        return $this->hasOne(TenagaMedis::class, 'profile_id');
    }
}

