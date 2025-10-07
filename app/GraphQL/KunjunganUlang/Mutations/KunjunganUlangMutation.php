<?php

namespace App\GraphQL\KunjunganUlang\Mutations;
use App\Models\KunjunganUlang\KunjunganUlang;

class KunjunganUlangMutation {
    public function restore($_, array $args): ?KunjunganUlang {
        return KunjunganUlang::withTrashed()->find($args['id'])?->restore()
        ? KunjunganUlang::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?KunjunganUlang{
        $KunjunganUlang = KunjunganUlang::withTrashed()->find($args['id']);
        if ($KunjunganUlang) {
            $KunjunganUlang->forceDelete();
            return $KunjunganUlang;
        }
        return null;
    }
}