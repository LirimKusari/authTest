<?php

namespace Tests;

use App\Authorization\Catalog\Roles;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function seedAccess(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);
    }

    protected function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    protected function applicant(array $attributes = []): User
    {
        return $this->userWithRole(Roles::APPLICANT, $attributes);
    }

    protected function manager(array $attributes = []): User
    {
        return $this->userWithRole(Roles::MANAGER, $attributes);
    }

    protected function admin(array $attributes = []): User
    {
        return $this->userWithRole(Roles::ADMIN, $attributes);
    }
}
