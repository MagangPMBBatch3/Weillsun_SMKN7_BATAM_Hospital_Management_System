<?php

namespace App\GraphQL\DetailPembelianObat\Mutations;
use App\Models\DetailPembelianObat\DetailPembelianObat;

class DetailPembelianObatMutation {
    public function restore($_, array $args): ?DetailPembelianObat {
        return DetailPembelianObat::withTrashed()->find($args['id'])?->restore()
        ? DetailPembelianObat::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?DetailPembelianObat{
        $DetailPembelianObat = DetailPembelianObat::withTrashed()->find($args['id']);
        if ($DetailPembelianObat) {
            $DetailPembelianObat->forceDelete();
            return $DetailPembelianObat;
        }
        return null;
    }
}