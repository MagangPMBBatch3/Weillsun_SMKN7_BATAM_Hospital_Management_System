<?php

namespace App\GraphQL\User\Queries;

use App\Models\User;

class UserQuery
{

    public function all($_, array $args)
    {
        $query = User::query();

        if (!empty($args['search'])) {
            $search = $args['search'];

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('role', 'like', "%$search%");
            });
        }
        return $query->get();
    }

   public function allArsip($_, array $args)
   {
       return User::onlyTrashed()->get();
   }
}