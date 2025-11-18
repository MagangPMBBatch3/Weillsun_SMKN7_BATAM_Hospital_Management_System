<?php

namespace App\GraphQL\RawatInap\Queries;

use App\Models\RawatInap\RawatInap;

class RawatInapQuery
{
    public function all($_, array $args)
    {
        $query = RawatInap::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal_masuk', 'like', "%$search%")
                    ->orWhere('tanggal_keluar', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            })
                ->orWhereHas('pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                })
                ->orWhereHas('ruangan', function ($q) use ($search) {
                    $q->where('nama_ruangan', 'like', "%$search%");
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
        $query = RawatInap::onlyTrashed();
        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal_masuk', 'like', "%$search%")
                    ->orWhere('tanggal_keluar', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            })
                ->orWhereHas('pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                })
                ->orWhereHas('ruangan', function ($q) use ($search) {
                    $q->where('nama_ruangan', 'like', "%$search%");
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
