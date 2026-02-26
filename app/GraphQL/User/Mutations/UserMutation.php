<?php

namespace App\GraphQL\User\Mutations;
use App\Models\User;

class UserMutation {

    // Kalau user dengan id ini ada dan berhasil direstore,
    // maka ambil usernya lagi dan kembalikan.
    // Tapi kalau gagal, kembalikan null.
    
    public function restore($_, array $args):  ?User 
    {
        return User::withTrashed()->find($args['id'])?->restore()
            ? User::find($args['id'])
            : null;
    }

    // Cari user, walaupun dia sudah dihapus abis tu delete permanen
    public function forceDelete($_, array $args): ?User
    {
        $user = User::withTrashed()->find($args['id']);
        if ($user) {
            $user->forceDelete();
            return $user;
        }
        return null;
    }
}
?>