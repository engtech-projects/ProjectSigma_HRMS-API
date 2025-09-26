<?php

namespace App\Http\Controllers;

use App\Models\PhilhealthContribution;
use App\Http\Requests\StorePhilhealthContributionRequest;
use App\Http\Requests\UpdatePhilhealthContributionRequest;
use App\Http\Resources\TempAllData;

class PhilhealthContributionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sss = PhilhealthContribution::paginate(config("app.pagination_per_page"));
        return TempAllData::collection($sss)->additional([
            'success' => true,
            'message' => 'Philhealth Contribution fetched.',
        ]);
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
    public function store(StorePhilhealthContributionRequest $request)
    {
        $philhealth = new PhilhealthContribution();
        $philhealth->fill($request->validated());
        $data = json_decode('{}');
        if (!$philhealth->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $philhealth;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $philhealth = PhilhealthContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($philhealth)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $philhealth;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PhilhealthContribution $philhealthContribution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePhilhealthContributionRequest $request, $id)
    {
        $philhealth = PhilhealthContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($philhealth)) {
            $philhealth->fill($request->validated());
            if ($philhealth->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $philhealth;
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
        $philhealth = PhilhealthContribution::find($id);
        $data = json_decode('{}');
        if (!is_null($philhealth)) {
            if ($philhealth->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $philhealth;
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
