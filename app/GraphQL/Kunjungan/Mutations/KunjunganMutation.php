<?php

namespace App\GraphQL\Kunjungan\Mutations;
use App\Models\Kunjungan\Kunjungan;

class KunjunganMutation {
    public function restore($_, array $args): ?Kunjungan {
        return Kunjungan::withTrashed()->find($args['id'])?->restore()
        ? Kunjungan::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?Kunjungan{
        $Kunjungan = Kunjungan::withTrashed()->find($args['id']);
        if ($Kunjungan) {
            $Kunjungan->forceDelete();
            return $Kunjungan;
        }
        return null;
    }
}