<?php

namespace App\Services;

use App\Authorization\Enforcement\Authorizer;
use App\Authorization\Enforcement\ReadProjection;
use App\Authorization\Enforcement\WriteGuard;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Repositories\ApplicationRepository;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    public function __construct(
        private ApplicationRepository $applications,
        private Authorizer $authorizer,
        private ReadProjection $projection,
        private WriteGuard $writeGuard,
    ) {}

    /**
     * @return array{data: list<array<string, mixed>>}
     */
    public function list(User $user): array
    {
        $this->authorizer->assertPage($user, 'application.index');

        return $this->projection->collection($user, 'application.index', $this->applications->allVisible($user));
    }

    /**
     * @return array{data: list<array<string, mixed>>}
     */
    public function listForJob(User $user, Job $job): array
    {
        $this->authorizer->assertPage($user, 'application.index');
        $this->authorizer->assertAction($user, 'view', $job);

        return $this->projection->collection(
            $user,
            'application.index',
            $this->applications->visibleForJob($user, $job),
        );
    }

    /**
     * @return array{data: array<string, mixed>, abilities: array<string, mixed>}
     */
    public function show(User $user, Application $application): array
    {
        $this->authorizer->assertPage($user, 'application.show');
        $this->authorizer->assertAction($user, 'view', $application);

        return $this->projection->present($user, 'application.show', $application);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{data: array<string, mixed>, abilities: array<string, mixed>}
     */
    public function create(User $user, Job $job, array $input): array
    {
        $this->authorizer->assertPage($user, 'application.store');
        $this->authorizer->assertAction($user, 'create', [Application::class, $job]);

        if ($this->applications->existsFor($job, $user)) {
            throw ValidationException::withMessages([
                'job_id' => ['You have already applied to this job.'],
            ]);
        }

        $attributes = $this->writeGuard->permitted($user, 'application', $input);

        $attributes['job_id'] = $job->id;
        $attributes['user_id'] = $user->id;
        $attributes['status'] = Application::STATUS_SUBMITTED;

        $application = $this->applications->create($attributes);

        return $this->projection->present($user, 'application.show', $application);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{data: array<string, mixed>, abilities: array<string, mixed>}
     */
    public function update(User $user, Application $application, array $input): array
    {
        $this->authorizer->assertPage($user, 'application.update');
        $this->authorizer->assertAction($user, 'update', $application);

        $application = $this->applications->update(
            $application,
            $this->writeGuard->permitted($user, 'application', $input),
        );

        return $this->projection->present($user, 'application.show', $application);
    }

    public function delete(User $user, Application $application): void
    {
        $this->authorizer->assertPage($user, 'application.destroy');
        $this->authorizer->assertAction($user, 'delete', $application);

        $this->applications->delete($application);
    }
}
