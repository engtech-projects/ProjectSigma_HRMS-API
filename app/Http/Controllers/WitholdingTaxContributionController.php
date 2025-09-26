<?php

namespace App\Http\Controllers;

use App\Models\WitholdingTaxContribution;
use App\Http\Requests\StoreWitholdingTaxContributionRequest;
use App\Http\Requests\UpdateWitholdingTaxContributionRequest;
use App\Http\Resources\TempAllData;

class WitholdingTaxContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $witholdingtax = WitholdingTaxContribution::paginate(config("app.pagination_per_page"));
        return TempAllData::collection($witholdingtax)->additional([
            'success' => true,
            'message' => 'Witholding Tax Contribution fetched.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWitholdingTaxContributionRequest $request)
    {
        $witholdingtax = new WitholdingTaxContribution();
        $valdata = $request->validated();
        $witholdingtax->fill($valdata);
        $data = json_decode('{}');
        if (!$witholdingtax->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $witholdingtax;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $witholdingtax = WitholdingTaxContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($witholdingtax)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $witholdingtax;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWitholdingTaxContributionRequest $request, $id)
    {
        $witholdingtax = WitholdingTaxContribution::find($id);
        $witholdingtax->fill($request->validated());
        $data = json_decode('{}');
        if (!is_null($witholdingtax)) {
            if ($witholdingtax->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $witholdingtax;
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
        $witholdingtax = WitholdingTaxContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($witholdingtax)) {
            if ($witholdingtax->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $witholdingtax;
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
