<?php

namespace App\GraphQL\Pasien\Mutations;
use App\Models\Pasien\Pasien;

class PasienMutation {
    public function restore($_, array $args): ?Pasien {
        return Pasien::withTrashed()->find($args['id'])?->restore()
        ? Pasien::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?Pasien{
        $Pasien = Pasien::withTrashed()->find($args['id']);
        if ($Pasien) {
            $Pasien->forceDelete();
            return $Pasien;
        }
        return null;
    }
}