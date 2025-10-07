<?php

namespace App\GraphQL\ResepObat\Queries;

use App\Models\ResepObat\ResepObat;

class ResepObatQuery
{
    public function all($_, array $args)  
    {
        $query = ResepObat::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('obat_id', 'like', "%$search%")
                    ->orWhere('jumlah', 'like', "%$search%")
                    ->orWhere('aturan_pakai', 'like', "%$search%");
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
       return ResepObat::onlyTrashed()->get();
   }
}
