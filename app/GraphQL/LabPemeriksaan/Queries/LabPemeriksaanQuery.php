<?php

namespace App\GraphQL\LabPemeriksaan\Queries;

use App\Models\LabPemeriksaan\LabPemeriksaan;

class LabPemeriksaanQuery
{
    public function all($_, array $args)
    {
        $query = LabPemeriksaan::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('jenis_pemeriksaan', 'like', "%$search%")
                    ->orWhere('hasil', 'like', "%$search%")
                    ->orWhere('tanggal', 'like', "%$search%")
                    ->orWhere('biaya_lab', 'like', "%$search%");
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
        return LabPemeriksaan::onlyTrashed();
    }
}
