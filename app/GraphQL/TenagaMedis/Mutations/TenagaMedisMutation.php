<?php

namespace App\GraphQL\TenagaMedis\Mutations;
use App\Models\TenagaMedis\TenagaMedis;

class TenagaMedisMutation {
    public function restore($_, array $args): ?TenagaMedis {
        return TenagaMedis::withTrashed()->find($args['id'])?->restore()
        ? TenagaMedis::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?TenagaMedis{
        $TenagaMedis = TenagaMedis::withTrashed()->find($args['id']);
        if ($TenagaMedis) {
            $TenagaMedis->forceDelete();
            return $TenagaMedis;
        }
        return null;
    }
}