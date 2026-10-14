<?php

namespace App\Authorization\Enforcement;

use App\Authorization\Catalog\PermissionCatalog;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class Authorizer
{
    public function __construct(private PermissionCatalog $catalog) {}

    public function allows(User $user, string $permission): bool
    {
        return $user->can($permission);
    }

    public function assertPage(User $user, string $pageKey): void
    {
        $page = $this->catalog->page($pageKey);

        if (! $this->allows($user, $page->requiredPermission)) {
            throw new AuthorizationException;
        }
    }

    public function assertAction(User $user, string $action, mixed $arguments): void
    {
        if (Gate::forUser($user)->denies($action, $arguments)) {
            throw new AuthorizationException;
        }
    }

    /**
     * @return list<string>
     */
    public function visibleSections(User $user, string $pageKey): array
    {
        $visible = [];

        foreach ($this->catalog->sectionsFor($pageKey) as $section) {
            if ($this->allows($user, $section->requiredPermission)) {
                $visible[] = $section->key;
            }
        }

        return $visible;
    }

    /**
     * @return list<string>
     */
    public function readMask(User $user, string $resource): array
    {
        return $this->mask($user, $resource, 'read');
    }

    /**
     * @return list<string>
     */
    public function writeMask(User $user, string $resource): array
    {
        return $this->mask($user, $resource, 'write');
    }

    public function abilities(User $user, string $pageKey, ?Model $model = null): AbilityMap
    {
        $page = $this->catalog->page($pageKey);
        $resource = $model ? $this->resourceOf($model) : $page->resource;

        return new AbilityMap(
            actions: $this->actions($user, $resource, $model),
            sections: $this->visibleSections($user, $pageKey),
            readableFields: $this->readMask($user, $resource),
            writableFields: $this->writeMask($user, $resource),
        );
    }

    /**
     * @return array<string, bool>
     */
    private function actions(User $user, string $resource, ?Model $model): array
    {
        return [
            'view' => $model instanceof Job || $model instanceof Application
                ? Gate::forUser($user)->allows('view', $model)
                : $this->allows($user, $this->catalog->page("{$resource}.index")->requiredPermission),
            'create' => $this->allowsCreate($user, $resource, $model),
            'update' => $model !== null && Gate::forUser($user)->allows('update', $model),
            'delete' => $model !== null && Gate::forUser($user)->allows('delete', $model),
        ];
    }

    private function allowsCreate(User $user, string $resource, ?Model $model): bool
    {
        return match (true) {
            $resource === 'job' => Gate::forUser($user)->allows('create', Job::class),
            $model instanceof Job => Gate::forUser($user)->allows('create', [Application::class, $model]),
            default => false,
        };
    }

    /**
     * @return list<string>
     */
    private function mask(User $user, string $resource, string $direction): array
    {
        $attributes = [];

        foreach ($this->catalog->fieldSetsFor($resource) as $fieldSet) {
            $permission = $direction === 'write' ? $fieldSet->writePermission : $fieldSet->readPermission;

            if ($permission === null || ! $this->allows($user, $permission)) {
                continue;
            }

            foreach ($fieldSet->attributes as $attribute) {
                $attributes[$attribute] = true;
            }
        }

        return array_keys($attributes);
    }

    public function resourceOf(Model $model): string
    {
        return match ($model::class) {
            Job::class => 'job',
            Application::class => 'application',
            default => throw new InvalidArgumentException('No permission resource for ['.$model::class.'].'),
        };
    }
}
