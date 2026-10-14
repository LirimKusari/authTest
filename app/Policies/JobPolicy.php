<?php

namespace App\Policies;

use App\Authorization\Catalog\Permissions;
use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    public function view(User $user, Job $job): bool
    {
        if (! $user->can(Permissions::JOB_VIEW)) {
            return false;
        }

        if ($job->isPublished()) {
            return true;
        }

        return $user->can(Permissions::JOB_VIEW_UNPUBLISHED);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::JOB_CREATE);
    }

    public function update(User $user, Job $job): bool
    {
        return $user->can(Permissions::JOB_UPDATE);
    }

    public function delete(User $user, Job $job): bool
    {
        return $user->can(Permissions::JOB_DELETE);
    }
}
