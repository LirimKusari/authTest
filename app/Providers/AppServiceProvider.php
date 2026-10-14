<?php

namespace App\Providers;

use App\Authorization\Catalog\PermissionCatalog;
use App\Models\Application;
use App\Models\Job;
use App\Policies\ApplicationPolicy;
use App\Policies\JobPolicy;
use App\Repositories\ApplicationRepository;
use App\Repositories\JobRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PermissionCatalog::class);
    }

    public function boot(): void
    {
        Gate::policy(Job::class, JobPolicy::class);
        Gate::policy(Application::class, ApplicationPolicy::class);

        Route::bind('job', function (string $value) {
            $user = request()->user();
            abort_if($user === null, 401);

            return app(JobRepository::class)->findVisibleOrFail($user, (int) $value);
        });

        Route::bind('application', function (string $value) {
            $user = request()->user();
            abort_if($user === null, 401);

            return app(ApplicationRepository::class)->findVisibleOrFail($user, (int) $value);
        });
    }
}
