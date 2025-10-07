<?php

namespace App\GraphQL\Supplier\Mutations;
use App\Models\Supplier\Supplier;

class SupplierMutation {
    public function restore($_, array $args): ?Supplier {
        return Supplier::withTrashed()->find($args['id'])?->restore()
        ? Supplier::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?Supplier{
        $Supplier = Supplier::withTrashed()->find($args['id']);
        if ($Supplier) {
            $Supplier->forceDelete();
            return $Supplier;
        }
        return null;
    }
}