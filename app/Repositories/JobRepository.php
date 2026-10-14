<?php

namespace App\Repositories;

use App\Authorization\Scopes\JobRowScope;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class JobRepository
{
    public function __construct(private JobRowScope $scope) {}

    /**
     * @return Collection<int, Job>
     */
    public function allVisible(User $user): Collection
    {
        return $this->scope
            ->constrain(Job::query(), $user)
            ->orderByDesc('id')
            ->get();
    }

    public function findVisibleOrFail(User $user, int $id): Job
    {
        return $this->scope
            ->constrain(Job::query(), $user)
            ->whereKey($id)
            ->firstOrFail();
    }

    public function create(array $attributes): Job
    {
        return Job::query()->create($attributes);
    }

    public function update(Job $job, array $attributes): Job
    {
        $job->fill($attributes);
        $job->save();

        return $job->refresh();
    }

    public function delete(Job $job): void
    {
        $job->delete();
    }
}
