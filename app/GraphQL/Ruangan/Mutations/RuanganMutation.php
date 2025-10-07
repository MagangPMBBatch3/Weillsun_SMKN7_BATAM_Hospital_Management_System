<?php

namespace App\GraphQL\Ruangan\Mutations;
use App\Models\Ruangan\Ruangan;

class RuanganMutation {
    public function restore($_, array $args): ?Ruangan {
        return Ruangan::withTrashed()->find($args['id'])?->restore()
        ? Ruangan::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?Ruangan{
        $Ruangan = Ruangan::withTrashed()->find($args['id']);
        if ($Ruangan) {
            $Ruangan->forceDelete();
            return $Ruangan;
        }
        return null;
    }
}