<?php

namespace App\GraphQL\RawatInap\Mutations;
use App\Models\RawatInap\RawatInap;

class RawatInapMutation {
    public function restore($_, array $args): ?RawatInap {
        return RawatInap::withTrashed()->find($args['id'])?->restore()
        ? RawatInap::find($args['id'])
        : null;
    }

    // public function forceDelete($_, array $args): ?RawatInap{
    //     $RawatInap = RawatInap::withTrashed()->find($args['id']);
    //     if ($RawatInap) {
    //         $RawatInap->forceDelete();
    //         return $RawatInap;
    //     }
    //     return null;
    // }
}