<?php

namespace App\GraphQL\PembayaranSupplier\Mutations;
use App\Models\PembayaranSupplier\PembayaranSupplier;

class PembayaranSupplierMutation {
    public function restore($_, array $args): ?PembayaranSupplier {
        return PembayaranSupplier::withTrashed()->find($args['id'])?->restore()
        ? PembayaranSupplier::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?PembayaranSupplier{
        $PembayaranSupplier = PembayaranSupplier::withTrashed()->find($args['id']);
        if ($PembayaranSupplier) {
            $PembayaranSupplier->forceDelete();
            return $PembayaranSupplier;
        }
        return null;
    }
}