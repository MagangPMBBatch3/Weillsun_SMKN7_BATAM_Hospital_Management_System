<?php

namespace App\GraphQL\TenagaMedis\Queries;

use App\Models\TenagaMedis\TenagaMedis;

class TenagaMedisQuery
{
    public function all($_, array $args)
    {
        $query = TenagaMedis::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('profile_id', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhere('spesialisasi', 'like', "%$search%")
                    ->orWhere('no_str', 'like', "%$search%");
            })
                ->orWhereHas('profile', function ($q) use ($search) {
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
        $query = TenagaMedis::onlyTrashed();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('profile_id', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%")
                    ->orWhere('spesialisasi', 'like', "%$search%")
                    ->orWhere('no_str', 'like', "%$search%");
            })
                ->orWhereHas('profile', function ($q) use ($search) {
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
