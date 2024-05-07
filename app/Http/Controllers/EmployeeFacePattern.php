<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeFacePatternRequest;
use App\Http\Services\EmployeePatternService;
use Illuminate\Http\Request;

class EmployeeFacePattern extends Controller
{
    protected $facePatternService;

    public function __construct(EmployeePatternService $facePatternService)
    {
        $this->facePatternService = $facePatternService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json($_SERVER);
        $data = json_decode('{}');
        $data = $this->facePatternService->getAll();
        $data->message = "Successfull Fetch";
        $data->success = true;
        $data->data = $data;
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeFacePatternRequest $request)
    {
        $data = json_decode('{}');
        $validatedData = $request->validated();
        $data->success = $this->facePatternService->insert($validatedData);
        if ($data)
        {
            $data->message = "Successfully Save";
            return response()->json($data);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
