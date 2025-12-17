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

    public function checkDuplicate($_, array $args)
    {
        $pembelian_id = $args['pembelian_id'];
        $tanggal_bayar = $args['tanggal_bayar'];
        $exclude_id = $args['exclude_id'] ?? null;

        $query = PembayaranSupplier::where('pambelian_id', $pembelian_id)
            ->where('tanggal_bayar', $tanggal_bayar);

        // Jika exclude_id diberikan (untuk update), exclude record dengan ID tersebut
        if ($exclude_id) {
            $query->where('id', '!=', $exclude_id);
        }

        // Return true jika data duplikat ditemukan, false jika tidak
        return $query->exists();
    }
}
