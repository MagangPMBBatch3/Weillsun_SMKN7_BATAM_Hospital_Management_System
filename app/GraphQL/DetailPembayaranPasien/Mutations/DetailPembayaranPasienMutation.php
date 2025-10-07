<?php

namespace App\GraphQL\DetailPembayaranPasien\Mutations;
use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;

class DetailPembayaranPasienMutation {
    public function restore($_, array $args): ?DetailPembayaranPasien {
        return DetailPembayaranPasien::withTrashed()->find($args['id'])?->restore()
        ? DetailPembayaranPasien::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?DetailPembayaranPasien{
        $DetailPembayaranPasien = DetailPembayaranPasien::withTrashed()->find($args['id']);
        if ($DetailPembayaranPasien) {
            $DetailPembayaranPasien->forceDelete();
            return $DetailPembayaranPasien;
        }
        return null;
    }
}