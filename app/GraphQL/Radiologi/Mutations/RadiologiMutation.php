<?php

namespace App\GraphQL\Radiologi\Mutations;
use App\Models\Radiologi\Radiologi;

class RadiologiMutation {
    public function restore($_, array $args): ?Radiologi {
        return Radiologi::withTrashed()->find($args['id'])?->restore()
        ? Radiologi::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?Radiologi{
        $Radiologi = Radiologi::withTrashed()->find($args['id']);
        if ($Radiologi) {
            $Radiologi->forceDelete();
            return $Radiologi;
        }
        return null;
    }
}