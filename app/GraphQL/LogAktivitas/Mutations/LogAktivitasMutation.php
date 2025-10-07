<?php

namespace App\GraphQL\LogAktivitas\Mutations;
use App\Models\LogAktivitas\LogAktivitas;

class LogAktivitasMutation {
    public function restore($_, array $args): ?LogAktivitas {
        return LogAktivitas::withTrashed()->find($args['id'])?->restore()
        ? LogAktivitas::find($args['id'])
        : null;
    }

    public function forceDelete($_, array $args): ?LogAktivitas{
        $LogAktivitas = LogAktivitas::withTrashed()->find($args['id']);
        if ($LogAktivitas) {
            $LogAktivitas->forceDelete();
            return $LogAktivitas;
        }
        return null;
    }
}