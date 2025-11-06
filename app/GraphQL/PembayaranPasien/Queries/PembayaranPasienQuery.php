<?php

namespace App\GraphQL\PembayaranPasien\Queries;

use App\Models\PembayaranPasien\PembayaranPasien;

class PembayaranPasienQuery
{
    public function all($_, array $args)
    {
        $query = PembayaranPasien::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('total_biaya', 'like', "%$search%")
                    ->orWhere('metode_bayar', 'like', "%$search%")
                    ->orWhere('tanggal_bayar', 'like', "%$search%");
            });
        }

        $perPage = $args['first'] ?? 10;
        $page = $args['page'] ?? 1;

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'paginatorInfo' => [
                'hasMorePages' => $paginator->hasMorePages(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    public function allArchive($_, array $args)
    {
        return PembayaranPasien::onlyTrashed();
    }
}
