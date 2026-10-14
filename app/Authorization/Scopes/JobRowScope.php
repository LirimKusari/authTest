<?php

namespace App\Authorization\Scopes;

use App\Authorization\Catalog\Permissions;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class JobRowScope
{
    /**
     * @param  Builder<Job>  $query
     * @return Builder<Job>
     */
    public function constrain(Builder $query, User $user): Builder
    {
        if ($user->can(Permissions::JOB_VIEW_UNPUBLISHED)) {
            return $query;
        }

        return $query->where('status', Job::STATUS_PUBLISHED);
    }
}
