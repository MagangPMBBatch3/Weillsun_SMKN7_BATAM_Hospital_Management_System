<?php

namespace App\GraphQL\JadwalTenagaMedis\Queries;

use App\Models\JadwalTenagaMedis\JadwalTenagaMedis;

class JadwalTenagaMedisQuery
{
    public function all($_, array $args)
    {
        $query = JadwalTenagaMedis::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('jam_mulai', 'like', "%$search%")
                    ->orWhere('jam_selesai', 'like', "%$search%");
            })
            ->orWhereHas('tenagaMedis.profile', function ($q) use ($search) {
                    $q->where('nickname', 'like', "%$search%");
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
        $query = JadwalTenagaMedis::onlyTrashed();
        
        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('jam_mulai', 'like', "%$search%")
                    ->orWhere('jam_selesai', 'like', "%$search%");
            })
            ->orWhereHas('tenagaMedis.profile', function ($q) use ($search) {
                    $q->where('nickname', 'like', "%$search%");
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
