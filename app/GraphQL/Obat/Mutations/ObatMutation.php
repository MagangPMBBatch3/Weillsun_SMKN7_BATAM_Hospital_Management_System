<?php

namespace App\GraphQL\Obat\Mutations;
use App\Models\Obat\Obat;

class ObatMutation {
    public function restore($_, array $args): ?Obat {
        return Obat::withTrashed()->find($args['id'])?->restore()
        ? Obat::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?Obat{
        $Obat = Obat::withTrashed()->find($args['id']);
        if ($Obat) {
            $Obat->forceDelete();
            return $Obat;
        }
        return null;
    }
}