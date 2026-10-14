<?php

namespace App\Services;

use App\Authorization\Catalog\Roles;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private UserRepository $users) {}

    /**
     * @param  array{name: string, email: string, password: string}  $input
     * @return array{data: array<string, mixed>}
     */
    public function register(array $input): array
    {
        $user = $this->users->create($input);
        $user->assignRole(Roles::APPLICANT);

        Auth::login($user);
        session()->regenerate();

        return $this->payload($user->fresh());
    }

    /**
     * @param  array{email: string, password: string}  $credentials
     * @return array{data: array<string, mixed>}
     */
    public function login(array $credentials): array
    {
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        session()->regenerate();

        return $this->payload(Auth::user());
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * @return array{data: array<string, mixed>}
     */
    public function me(User $user): array
    {
        return $this->payload($user);
    }

    /**
     * @return array{csrf_token: string}
     */
    public function csrfCookie(): array
    {
        return ['csrf_token' => csrf_token()];
    }

    /**
     * @return array{data: array<string, mixed>}
     */
    private function payload(User $user): array
    {
        return [
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
            ],
        ];
    }
}
