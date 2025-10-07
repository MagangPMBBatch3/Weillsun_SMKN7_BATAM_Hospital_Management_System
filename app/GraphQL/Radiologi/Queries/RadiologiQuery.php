<?php

namespace App\GraphQL\Radiologi\Queries;

use App\Models\Radiologi\Radiologi;

class RadiologiQuery
{
    public function all($_, array $args)  
    {
        $query = Radiologi::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('jenis_radiologi', 'like', "%$search%")
                    ->orWhere('hasil', 'like', "%$search%")
                    ->orWhere('tanggal', 'like', "%$search%")
                    ->orWhere('biaya_radiologi', 'like', "%$search%");
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
       return Radiologi::onlyTrashed()->get();
   }
}
