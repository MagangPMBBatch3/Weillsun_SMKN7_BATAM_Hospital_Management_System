<?php

namespace App\GraphQL\Supplier\Queries;

use App\Models\Supplier\Supplier;

class SupplierQuery
{
    public function all($_, array $args)
    {
        $query = Supplier::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%$search%")
                    ->orWhere('alamat', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('telepon', 'like', "%$search%");
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
        $query = Supplier::onlyTrashed();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%$search%")
                    ->orWhere('alamat', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('telepon', 'like', "%$search%");
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
