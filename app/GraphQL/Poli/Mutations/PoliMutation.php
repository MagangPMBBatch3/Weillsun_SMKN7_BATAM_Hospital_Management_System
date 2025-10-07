<?php

namespace App\GraphQL\Poli\Mutations;
use App\Models\Poli\Poli;

class PoliMutation {
    public function restore($_, array $args): ?Poli {
        return Poli::withTrashed()->find($args['id'])?->restore()
        ? Poli::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?Poli{
        $Poli = Poli::withTrashed()->find($args['id']);
        if ($Poli) {
            $Poli->forceDelete();
            return $Poli;
        }
        return null;
    }
}