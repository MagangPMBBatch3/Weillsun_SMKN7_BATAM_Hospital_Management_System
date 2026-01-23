<?php

namespace App\GraphQL\JadwalTenagaMedis\Queries;

use App\Models\JadwalTenagaMedis\JadwalTenagaMedis;

class JadwalTenagaMedisQuery
{
    public function all($_, array $args)
    {
        $query = JadwalTenagaMedis::query();

        // Filter by tenaga_medis_id if provided (for non-admin users)
        if (!empty($args['tenagaMedisId'])) {
            $query->where('tenaga_medis_id', $args['tenagaMedisId']);
        }

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('jam_mulai', 'like', "%$search%")
                    ->orWhere('jam_selesai', 'like', "%$search%");
            })
                ->orWhereHas('tenagaMedis.profile', function ($q) use ($search) {
                    $q->where('nickname', 'like', "%$search%");
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

    public function getJadwalByTenagaMedisAndHari($_, array $args)
    {
        $tenagaMedisId = $args['tenaga_medis_id'] ?? null;
        $hari = $args['hari'] ?? null;

        if (!$tenagaMedisId || $hari === null) {
            return [];
        }

        return JadwalTenagaMedis::where('tenaga_medis_id', $tenagaMedisId)
            ->where('hari', $hari)
            ->get()
            ->toArray();
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
