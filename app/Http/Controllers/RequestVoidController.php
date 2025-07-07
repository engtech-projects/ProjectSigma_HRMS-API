<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetRequestVoidRequest;
use App\Models\RequestVoid;
use App\Http\Requests\StoreRequestVoidRequest;
use App\Http\Requests\UpdateRequestVoidRequest;
use App\Http\Resources\RequestVoidResource;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\JsonResponse;

class RequestVoidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetRequestVoidRequest $request)
    {
        $validatedData = $request->validated();
        $data = RequestVoid::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->whereHas('employees', function ($query2) use ($validatedData) {
                $query2->where('created_by', $validatedData["employee_id"]);
            });
        })
        ->when($request->has('request_type') && $validatedData['request_type'] != '', function ($query) use ($validatedData) {
            return $query->whereDate('request_type', $validatedData['date_filter']);
        })
        ->orderBy("created_at", "DESC")
        ->paginate(15);
        return RequestVoidResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'Void Request fetched.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequestVoidRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RequestVoid $resource)
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Void Request fetched.',
            'data' => new RequestVoidResource($resource->load(["request"]))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequestVoidRequest $request, RequestVoid $resource)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RequestVoid $resource)
    {
        //
    }
    public function myRequests(GetRequestVoidRequest $request)
    {
        $validatedData = $request->validated();
        $data = RequestVoid::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->whereHas('employees', function ($query2) use ($validatedData) {
                $query2->where('created_by', $validatedData["employee_id"]);
            });
        })
        ->when($request->has('request_type') && $validatedData['request_type'] != '', function ($query) use ($validatedData) {
            return $query->whereDate('request_type', $validatedData['date_filter']);
        })
        ->myRequests()
        ->orderBy("created_at", "DESC")
        ->paginate(15);
        return RequestVoidResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'Void Request fetched.',
        ]);
    }
    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals(GetRequestVoidRequest $request)
    {
        $validatedData = $request->validated();
        $data = RequestVoid::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->whereHas('employees', function ($query2) use ($validatedData) {
                $query2->where('created_by', $validatedData["employee_id"]);
            });
        })
        ->when($request->has('request_type') && $validatedData['request_type'] != '', function ($query) use ($validatedData) {
            return $query->whereDate('request_type', $validatedData['date_filter']);
        })
        ->myApprovals()
        ->orderBy("created_at", "DESC")
        ->paginate(15);
        return RequestVoidResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'Void Request fetched.',
        ]);
    }
}
