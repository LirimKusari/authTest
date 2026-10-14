<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(private ApplicationService $applications) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->applications->list($request->user()));
    }

    public function indexForJob(Request $request, Job $job): JsonResponse
    {
        return response()->json($this->applications->listForJob($request->user(), $job));
    }

    public function show(Request $request, Application $application): JsonResponse
    {
        return response()->json($this->applications->show($request->user(), $application));
    }

    public function store(StoreApplicationRequest $request, Job $job): JsonResponse
    {
        return response()->json(
            $this->applications->create($request->user(), $job, $request->validated()),
            201,
        );
    }

    public function update(UpdateApplicationRequest $request, Application $application): JsonResponse
    {
        return response()->json(
            $this->applications->update($request->user(), $application, $request->validated()),
        );
    }

    public function destroy(Request $request, Application $application): JsonResponse
    {
        $this->applications->delete($request->user(), $application);

        return response()->json(null, 204);
    }
}
