<?php

namespace App\GraphQL\LabPemeriksaan\Mutations;
use App\Models\LabPemeriksaan\LabPemeriksaan;

class LabPemeriksaanMutation {
    public function restore($_, array $args): ?LabPemeriksaan {
        return LabPemeriksaan::withTrashed()->find($args['id'])?->restore()
        ? LabPemeriksaan::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?LabPemeriksaan{
        $LabPemeriksaan = LabPemeriksaan::withTrashed()->find($args['id']);
        if ($LabPemeriksaan) {
            $LabPemeriksaan->forceDelete();
            return $LabPemeriksaan;
        }
        return null;
    }
}