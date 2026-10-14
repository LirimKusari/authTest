<?php

namespace App\Authorization\Scopes;

use App\Authorization\Catalog\Permissions;
use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ApplicationRowScope
{
    /**
     * @param  Builder<Application>  $query
     * @return Builder<Application>
     */
    public function constrain(Builder $query, User $user): Builder
    {
        if ($user->can(Permissions::APPLICATION_VIEW_ANY)) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }
}
