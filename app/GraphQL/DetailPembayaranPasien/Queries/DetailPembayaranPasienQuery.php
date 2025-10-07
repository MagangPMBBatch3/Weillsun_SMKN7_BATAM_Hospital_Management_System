<?php

namespace App\GraphQL\DetailPembayaranPasien\Queries;

use App\Models\DetailPembayaranPasien\DetailPembayaranPasien;

class DetailPembayaranPasienQuery
{
    public function all($_, array $args)
    {
        $query = DetailPembayaranPasien::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tipe_biaya', 'like', "%$search%")
                    ->orWhere('jumlah', 'like', "%$search%")
                    ->orWhere('harga_satuan', 'like', "%$search%")
                    ->orWhere('subtotal', 'like', "%$search%");
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

    public function allArsip($_, array $args)
   {
       return DetailPembayaranPasien::onlyTrashed()->get();
   }
}
