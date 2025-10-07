<?php

namespace App\GraphQL\JadwalTenagaMedis\Mutations;
use App\Models\JadwalTenagaMedis\JadwalTenagaMedis;

class JadwalTenagaMedisMutation {
    public function restore($_, array $args): ?JadwalTenagaMedis {
        return JadwalTenagaMedis::withTrashed()->find($args['id'])?->restore()
        ? JadwalTenagaMedis::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?JadwalTenagaMedis{
        $JadwalTenagaMedis = JadwalTenagaMedis::withTrashed()->find($args['id']);
        if ($JadwalTenagaMedis) {
            $JadwalTenagaMedis->forceDelete();
            return $JadwalTenagaMedis;
        }
        return null;
    }
}