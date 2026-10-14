<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth) {}

    public function csrfCookie(): JsonResponse
    {
        return response()->json($this->auth->csrfCookie());
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return response()->json($this->auth->register($request->validated()), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return response()->json($this->auth->login($request->validated()));
    }

    public function logout(): JsonResponse
    {
        $this->auth->logout();

        return response()->json(null, 204);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->auth->me($request->user()));
    }
}
