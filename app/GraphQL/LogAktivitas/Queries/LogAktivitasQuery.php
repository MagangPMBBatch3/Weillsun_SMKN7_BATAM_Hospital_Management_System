<?php

namespace App\GraphQL\LogAktivitas\Queries;

use App\Models\LogAktivitas\LogAktivitas;

class LogAktivitasQuery
{
    public function all($_, array $args)  
    {
        $query = LogAktivitas::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('user_id', 'like', "%$search%")
                    ->orWhere('aktivitas', 'like', "%$search%")
                    ->orWhere('waktu', 'like', "%$search%");
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
       return LogAktivitas::onlyTrashed()->get();
   }
}
