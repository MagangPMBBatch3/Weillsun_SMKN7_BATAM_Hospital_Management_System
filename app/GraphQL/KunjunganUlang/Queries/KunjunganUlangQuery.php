<?php

namespace App\GraphQL\KunjunganUlang\Queries;

use App\Models\KunjunganUlang\KunjunganUlang;

class KunjunganUlangQuery
{
    public function all($_, array $args)  
    {
        $query = KunjunganUlang::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('tanggal_ulang', 'like', "%$search%")
                    ->orWhere('jam_ulang', 'like', "%$search%")
                    ->orWhere('catatan', 'like', "%$search%");
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
       return KunjunganUlang::onlyTrashed()->get();
   }
}
