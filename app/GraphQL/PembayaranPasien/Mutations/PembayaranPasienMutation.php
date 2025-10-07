<?php

namespace App\GraphQL\PembayaranPasien\Mutations;
use App\Models\PembayaranPasien\PembayaranPasien;

class PembayaranPasienMutation {
    public function restore($_, array $args): ?PembayaranPasien {
        return PembayaranPasien::withTrashed()->find($args['id'])?->restore()
        ? PembayaranPasien::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?PembayaranPasien{
        $PembayaranPasien = PembayaranPasien::withTrashed()->find($args['id']);
        if ($PembayaranPasien) {
            $PembayaranPasien->forceDelete();
            return $PembayaranPasien;
        }
        return null;
    }
}