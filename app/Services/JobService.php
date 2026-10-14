<?php

namespace App\Services;

use App\Authorization\Enforcement\Authorizer;
use App\Authorization\Enforcement\ReadProjection;
use App\Authorization\Enforcement\WriteGuard;
use App\Models\Job;
use App\Models\User;
use App\Repositories\JobRepository;

class JobService
{
    public function __construct(
        private JobRepository $jobs,
        private Authorizer $authorizer,
        private ReadProjection $projection,
        private WriteGuard $writeGuard,
    ) {}

    /**
     * @return array{data: list<array<string, mixed>>}
     */
    public function list(User $user): array
    {
        $this->authorizer->assertPage($user, 'job.index');

        return $this->projection->collection($user, 'job.index', $this->jobs->allVisible($user));
    }

    /**
     * @return array{data: array<string, mixed>, abilities: array<string, mixed>}
     */
    public function show(User $user, Job $job): array
    {
        $this->authorizer->assertPage($user, 'job.show');
        $this->authorizer->assertAction($user, 'view', $job);

        return $this->projection->present($user, 'job.show', $job);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{data: array<string, mixed>, abilities: array<string, mixed>}
     */
    public function create(User $user, array $input): array
    {
        $this->authorizer->assertPage($user, 'job.store');
        $this->authorizer->assertAction($user, 'create', Job::class);

        $attributes = $this->writeGuard->permitted($user, 'job', $input);
        $attributes['created_by'] = $user->id;
        $attributes['status'] ??= Job::STATUS_DRAFT;

        $job = $this->jobs->create($attributes);

        return $this->projection->present($user, 'job.show', $job);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{data: array<string, mixed>, abilities: array<string, mixed>}
     */
    public function update(User $user, Job $job, array $input): array
    {
        $this->authorizer->assertPage($user, 'job.update');
        $this->authorizer->assertAction($user, 'update', $job);

        $job = $this->jobs->update($job, $this->writeGuard->permitted($user, 'job', $input));

        return $this->projection->present($user, 'job.show', $job);
    }

    public function delete(User $user, Job $job): void
    {
        $this->authorizer->assertPage($user, 'job.destroy');
        $this->authorizer->assertAction($user, 'delete', $job);

        $this->jobs->delete($job);
    }
}
