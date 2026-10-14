<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(private JobService $jobs) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->jobs->list($request->user()));
    }

    public function show(Request $request, Job $job): JsonResponse
    {
        return response()->json($this->jobs->show($request->user(), $job));
    }

    public function showWithApplications(Request $request, Job $job): JsonResponse
    {
        return response()->json($this->jobs->showWithApplications($request->user(), $job));
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        return response()->json($this->jobs->create($request->user(), $request->validated()), 201);
    }

    public function update(UpdateJobRequest $request, Job $job): JsonResponse
    {
        return response()->json($this->jobs->update($request->user(), $job, $request->validated()));
    }

    public function destroy(Request $request, Job $job): JsonResponse
    {
        $this->jobs->delete($request->user(), $job);

        return response()->json(null, 204);
    }
}
