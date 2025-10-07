<?php

namespace App\GraphQL\ResepObat\Mutations;
use App\Models\ResepObat\ResepObat;

class ResepObatMutation {
    public function restore($_, array $args): ?ResepObat {
        return ResepObat::withTrashed()->find($args['id'])?->restore()
        ? ResepObat::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?ResepObat{
        $ResepObat = ResepObat::withTrashed()->find($args['id']);
        if ($ResepObat) {
            $ResepObat->forceDelete();
            return $ResepObat;
        }
        return null;
    }
}