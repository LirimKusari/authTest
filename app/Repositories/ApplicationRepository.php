<?php

namespace App\Repositories;

use App\Authorization\Scopes\ApplicationRowScope;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ApplicationRepository
{
    public function __construct(private ApplicationRowScope $scope) {}

    /**
     * @return Collection<int, Application>
     */
    public function allVisible(User $user): Collection
    {
        return $this->scope
            ->constrain(Application::query(), $user)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @return Collection<int, Application>
     */
    public function visibleForJob(User $user, Job $job): Collection
    {
        return $this->scope
            ->constrain(Application::query(), $user)
            ->where('job_id', $job->id)
            ->orderByDesc('id')
            ->get();
    }

    public function findVisibleOrFail(User $user, int $id): Application
    {
        return $this->scope
            ->constrain(Application::query(), $user)
            ->whereKey($id)
            ->firstOrFail();
    }

    public function existsFor(Job $job, User $user): bool
    {
        return Application::query()
            ->where('job_id', $job->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function create(array $attributes): Application
    {
        return Application::query()->create($attributes);
    }

    public function update(Application $application, array $attributes): Application
    {
        $application->fill($attributes);
        $application->save();

        return $application->refresh();
    }

    public function delete(Application $application): void
    {
        $application->delete();
    }
}
