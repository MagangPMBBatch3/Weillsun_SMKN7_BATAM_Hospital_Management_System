<?php

namespace App\GraphQL\PembelianObat\Queries;

use App\Models\PembelianObat\PembelianObat;

class PembelianObatQuery
{
    public function all($_, array $args)
    {
        $query = PembelianObat::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('total_biaya', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            })
                ->whereHas('supplier', function ($q) use ($search) {
                    $q->where('nama_supplier', 'like', "%$search%");
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
        $query = PembelianObat::onlyTrashed();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                    ->orWhere('total_biaya', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            })
                ->whereHas('supplier', function ($q) use ($search) {
                    $q->where('nama_supplier', 'like', "%$search%");
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

    public function checkDuplicate($_, array $args)
    {
        $supplier_id = $args['supplier_id'];
        $tanggal = $args['tanggal'];
        $exclude_id = $args['exclude_id'] ?? null;

        $query = PembelianObat::where('supplier_id', $supplier_id)
            ->where('tanggal', $tanggal);

        // Jika exclude_id diberikan (untuk update), exclude record dengan ID tersebut
        if ($exclude_id) {
            $query->where('id', '!=', $exclude_id);
        }

        // Return true jika data duplikat ditemukan, false jika tidak
        return $query->exists();
    }
}
