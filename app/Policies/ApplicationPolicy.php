<?php

namespace App\Policies;

use App\Authorization\Catalog\Permissions;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;

class ApplicationPolicy
{
    public function view(User $user, Application $application): bool
    {
        if (! $user->can(Permissions::APPLICATION_VIEW)) {
            return false;
        }

        if ($user->can(Permissions::APPLICATION_VIEW_ANY)) {
            return true;
        }

        return $application->user_id === $user->id;
    }

    public function create(User $user, Job $job): bool
    {
        return $user->can(Permissions::APPLICATION_CREATE) && $job->isPublished();
    }

    public function update(User $user, Application $application): bool
    {
        if (! $user->can(Permissions::APPLICATION_UPDATE)) {
            return false;
        }

        if ($user->can(Permissions::APPLICATION_VIEW_ANY)) {
            return true;
        }

        return $application->user_id === $user->id
            && $application->status === Application::STATUS_SUBMITTED;
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->can(Permissions::APPLICATION_DELETE);
    }
}
