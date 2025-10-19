<?php

namespace App\Http\Controllers;

use App\Models\SSSContribution;
use App\Http\Requests\StoreSSSContributionRequest;
use App\Http\Requests\UpdateSSSContributionRequest;
use App\Http\Resources\TempAllData;

class SSSContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sss = SSSContribution::paginate(config("app.pagination_per_page"));
        return TempAllData::collection($sss)->additional([
            'success' => true,
            'message' => 'SSS Contribution fetched.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSSSContributionRequest $request)
    {
        $sss = new SSSContribution();
        $sss->fill($request->validated());
        $data = json_decode('{}');
        if (!$sss->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $sss;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sss = SSSContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($sss)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $sss;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSSSContributionRequest $request, $id)
    {
        $sss = SSSContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($sss)) {
            $sss->fill($request->validated());
            if ($sss->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $sss;
                return response()->json($data);
            }
            $data->message = "Update failed.";
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
        $sss = SSSContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($sss)) {
            if ($sss->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $sss;
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
