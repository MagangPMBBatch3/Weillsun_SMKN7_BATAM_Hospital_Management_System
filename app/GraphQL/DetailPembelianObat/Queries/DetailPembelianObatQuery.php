<?php

namespace App\GraphQL\DetailPembelianObat\Queries;

use App\Models\DetailPembelianObat\DetailPembelianObat;

class DetailPembelianObatQuery
{
    public function all($_, array $args)
    {
        $query = DetailPembelianObat::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('jumlah', 'like', "%$search%")
                    ->orWhere('harga_satuan', 'like', "%$search%")
                    ->orWhere('harga_beli', 'like', "%$search%")
                    ->orWhere('subtotal', 'like', "%$search%");
            })
            ->orWhereHas('obat', function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%$search%");
            })
            ->orWhereHas('pembelianObat.supplier', function ($q) use ($search) {
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
        $query = DetailPembelianObat::onlyTrashed();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('jumlah', 'like', "%$search%")
                    ->orWhere('harga_satuan', 'like', "%$search%")
                    ->orWhere('harga_beli', 'like', "%$search%")
                    ->orWhere('subtotal', 'like', "%$search%");
            })
            ->orWhereHas('obat', function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%$search%");
            })
            ->orWhereHas('pembelianObat.supplier', function ($q) use ($search) {
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
}
