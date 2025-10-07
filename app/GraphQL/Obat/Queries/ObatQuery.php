<?php

namespace App\GraphQL\Obat\Queries;

use App\Models\Obat\Obat;

class ObatQuery
{
    public function all($_, array $args)  
    {
        $query = Obat::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('nama_obat', 'like', "%$search%")
                    ->orWhere('jenis_obat', 'like', "%$search%")
                    ->orWhere('harga', 'like', "%$search%")
                    ->orWhere('markup_harga', 'like', "%$search%")
                    ->orWhere('stok', 'like', "%$search%");
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
       return Obat::onlyTrashed()->get();
   }
}
