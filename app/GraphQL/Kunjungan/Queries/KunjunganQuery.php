<?php

namespace App\GraphQL\Kunjungan\Queries;

use App\Models\Kunjungan\Kunjungan;

class KunjunganQuery
{
    public function all($_, array $args)
    {
        $query = Kunjungan::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal_kunjungan', 'like', "%$search%")
                    ->orWhere('keluhan', 'like', "%$search%")
                    ->orWhere('biaya_konsultasi', 'like', "%$search%");
            })
            ->orWhereHas('pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
            })
            ->orWhereHas('poli', function ($q) use ($search) {
                    $q->where('nama_poli', 'like', "%$search%");
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
        $query = Kunjungan::onlyTrashed();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal_kunjungan', 'like', "%$search%")
                    ->orWhere('keluhan', 'like', "%$search%")
                    ->orWhere('biaya_konsultasi', 'like', "%$search%");
            })
            ->orWhereHas('pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
            })
            ->orWhereHas('poli', function ($q) use ($search) {
                    $q->where('nama_poli', 'like', "%$search%");
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
}
