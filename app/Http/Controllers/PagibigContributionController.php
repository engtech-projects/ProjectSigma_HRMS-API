<?php

namespace App\Http\Controllers;

use App\Models\PagibigContribution;
use App\Http\Requests\StorePagibigContributionRequest;
use App\Http\Requests\UpdatePagibigContributionRequest;
use App\Http\Resources\TempAllData;

class PagibigContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $pagibig = PagibigContribution::paginate(config("app.pagination_per_page"));
        return TempAllData::collection($pagibig)->additional([
            'success' => true,
            'message' => 'Pagibig Contribution fetched.',
        ]);
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
