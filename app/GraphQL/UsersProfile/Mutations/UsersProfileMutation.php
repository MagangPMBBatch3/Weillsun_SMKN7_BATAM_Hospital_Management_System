<?php

namespace App\GraphQL\UsersProfile\Mutations;

use App\Models\UsersProfile\UsersProfile;
use Illuminate\Support\Facades\Storage;

class UsersProfileMutation
{
    public function restore($_, array $args): ?UsersProfile
    {
        return UsersProfile::withTrashed()->find($args['id'])?->restore()
            ? UsersProfile::find($args['id'])
            : null;
    }

    public function forceDelete($_, array $args): ?UsersProfile
    {
        $profile = UsersProfile::withTrashed()->find($args['id']);
        if ($profile) {
            $profile->forceDelete();
            return $profile;
        }
        return null;
    }
}