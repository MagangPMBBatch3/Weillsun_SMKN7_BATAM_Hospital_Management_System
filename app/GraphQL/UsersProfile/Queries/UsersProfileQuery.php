<?php

namespace App\GraphQL\UsersProfile\Queries;

use App\Models\UsersProfile\UsersProfile;

class UsersProfileQuery
{
    public function all($_, array $args)  
    {
        $query = UsersProfile::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('user_id', 'like', "%$search%")
                    ->orWhere('nama', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('telepon', 'like', "%$search%")
                    ->orWhere('alamat', 'like', "%$search%");
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
       return UsersProfile::onlyTrashed()->get();
   }
}
