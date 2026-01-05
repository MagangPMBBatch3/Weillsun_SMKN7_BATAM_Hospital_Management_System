<?php

namespace App\GraphQL\LiburTenagaMedis\Mutations;
use App\Models\LiburTenagaMedis\LiburTenagaMedis;

class LiburTenagaMedisMutation {
    public function restore($_, array $args): ?LiburTenagaMedis {
        return LiburTenagaMedis::withTrashed()->find($args['id'])?->restore()
        ? LiburTenagaMedis::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?LiburTenagaMedis{
        $LiburTenagaMedis = LiburTenagaMedis::withTrashed()->find($args['id']);
        if ($LiburTenagaMedis) {
            $LiburTenagaMedis->forceDelete();
            return $LiburTenagaMedis;
        }
        return null;
    }
}