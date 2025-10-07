<?php

namespace App\GraphQL\RekamMedis\Mutations;
use App\Models\RekamMedis\RekamMedis;

class RekamMedisMutation {
    public function restore($_, array $args): ?RekamMedis {
        return RekamMedis::withTrashed()->find($args['id'])?->restore()
        ? RekamMedis::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?RekamMedis{
        $RekamMedis = RekamMedis::withTrashed()->find($args['id']);
        if ($RekamMedis) {
            $RekamMedis->forceDelete();
            return $RekamMedis;
        }
        return null;
    }
}