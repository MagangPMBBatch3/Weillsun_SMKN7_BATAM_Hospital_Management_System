<?php

namespace App\GraphQL\LiburTenagaMedis\Queries;

use App\Models\LiburTenagaMedis\LiburTenagaMedis;

class LiburTenagaMedisQuery
{
    public function all($_, array $args)
    {
        $query = LiburTenagaMedis::query();

        // Filter by tenaga_medis_id if provided (for non-admin users)
        if (!empty($args['tenagaMedisId'])) {
            $query->where('tenaga_medis_id', $args['tenagaMedisId']);
        }

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('jenis', 'like', "%$search%")
                    ->orWhere('keterangan', 'like', "%$search%")
                     ->orWhereHas('tenagaMedis.profile', function ($q) use ($search) {
                    $q->where('nickname', 'like', "%$search%");
                });
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
        $query = LiburTenagaMedis::onlyTrashed();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('jenis', 'like', "%$search%")
                    ->orWhere('keterangan', 'like', "%$search%")
                    ->orWhereHas('tenagaMedis.profile', function ($q) use ($search) {
                    $q->where('nickname', 'like', "%$search%");
                });
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
