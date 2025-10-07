<?php

namespace App\GraphQL\PembelianObat\Mutations;
use App\Models\PembelianObat\PembelianObat;

class PembelianObatMutation {
    public function restore($_, array $args): ?PembelianObat {
        return PembelianObat::withTrashed()->find($args['id'])?->restore()
        ? PembelianObat::find($args['id'])
        : null;
    }
    public function forceDelete($_, array $args): ?PembelianObat{
        $PembelianObat = PembelianObat::withTrashed()->find($args['id']);
        if ($PembelianObat) {
            $PembelianObat->forceDelete();
            return $PembelianObat;
        }
        return null;
    }
}