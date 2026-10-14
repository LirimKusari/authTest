<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccess();
    }

    public function test_guest_cannot_access_me(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_register_starts_a_session_as_applicant(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.email', 'ada@example.com')
            ->assertJsonPath('data.roles', ['applicant']);

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'ada@example.com');
    }

    public function test_login_and_logout_use_the_session(): void
    {
        $this->applicant([
            'email' => 'applicant@example.com',
        ]);

        $this->postJson('/api/login', [
            'email' => 'applicant@example.com',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('data.email', 'applicant@example.com');

        $this->getJson('/api/me')->assertOk();

        $this->postJson('/api/logout')->assertNoContent();

        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_csrf_cookie_returns_a_token(): void
    {
        $this->getJson('/api/csrf-cookie')
            ->assertOk()
            ->assertJsonStructure(['csrf_token']);
    }
}
