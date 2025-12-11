<?php

namespace App\GraphQL\PembayaranSupplier\Queries;

use App\Models\PembayaranSupplier\PembayaranSupplier;

class PembayaranSupplierQuery
{
    public function all($_, array $args)
    {
        $query = PembayaranSupplier::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('jumlah_bayar', 'like', "%$search%")
                    ->orWhere('metode_bayar', 'like', "%$search%")
                    ->orWhere('tanggal_bayar', 'like', "%$search%");
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
        $query = PembayaranSupplier::onlyTrashed();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('jumlah_bayar', 'like', "%$search%")
                    ->orWhere('metode_bayar', 'like', "%$search%")
                    ->orWhere('tanggal_bayar', 'like', "%$search%");
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
