<?php

namespace App\Http\Controllers;

use App\Models\PagibigContribution;
use App\Http\Requests\StorePagibigContributionRequest;
use App\Http\Requests\UpdatePagibigContributionRequest;

class PagibigContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $pagibig = PagibigContribution::paginate(15);
        $data = json_decode('{}');
        $data->message = "successfully fetch all";
        $data->success = true;
        $data->data = $pagibig;
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePagibigContributionRequest $request)
    {
        //
        $pagibig = new PagibigContribution();
        $pagibig->fill($request->validated());
        $data = json_decode('{}');
        if (!$pagibig->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $pagibig;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $pagibig = PagibigContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($pagibig)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $pagibig;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PagibigContribution $pagibigContribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePagibigContributionRequest $request, $id)
    {
        //
        $pagibig = PagibigContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($pagibig)) {
            $pagibig->fill($request->validated());
            if ($pagibig->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $pagibig;
                return response()->json($data);
            }
            $data->message = "Failed update.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Failed update.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $pagibig = PagibigContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($pagibig)) {
            if ($pagibig->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $pagibig;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data, 404);
    }
}
