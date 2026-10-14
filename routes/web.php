<?php

use App\Authorization\Catalog\Permissions;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Middleware\ForceJson;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->middleware(ForceJson::class)->group(function () {
    Route::get('/csrf-cookie', [AuthController::class, 'csrfCookie']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::get('/jobs', [JobController::class, 'index'])->middleware('permission:'.Permissions::JOB_VIEW);
        Route::post('/jobs', [JobController::class, 'store'])->middleware('permission:'.Permissions::JOB_CREATE);
        Route::get('/jobs/{job}', [JobController::class, 'show'])->middleware('permission:'.Permissions::JOB_VIEW);
        Route::get('/jobs/{job}/with-applications', [JobController::class, 'showWithApplications'])
            ->middleware('permission:'.Permissions::JOB_VIEW);
        Route::put('/jobs/{job}', [JobController::class, 'update'])->middleware('permission:'.Permissions::JOB_UPDATE);
        Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->middleware('permission:'.Permissions::JOB_DELETE);

        Route::get('/jobs/{job}/applications', [ApplicationController::class, 'indexForJob'])->middleware('permission:'.Permissions::APPLICATION_VIEW);
        Route::post('/jobs/{job}/applications', [ApplicationController::class, 'store'])->middleware('permission:'.Permissions::APPLICATION_CREATE);

        Route::get('/applications', [ApplicationController::class, 'index'])->middleware('permission:'.Permissions::APPLICATION_VIEW);
        Route::get('/applications/{application}', [ApplicationController::class, 'show'])->middleware('permission:'.Permissions::APPLICATION_VIEW);
        Route::put('/applications/{application}', [ApplicationController::class, 'update'])->middleware('permission:'.Permissions::APPLICATION_UPDATE);
        Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])->middleware('permission:'.Permissions::APPLICATION_DELETE);
    });
});
