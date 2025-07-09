<?php

namespace App\Http\Controllers;

use App\Enums\PostingStatusType;
use App\Http\Requests\OtherDeductionAllList;
use App\Models\OtherDeductionPayments;
use App\Http\Requests\StoreOtherDeductionPaymentsRequest;
use App\Http\Requests\UpdateOtherDeductionPaymentsRequest;
use App\Http\Resources\OtherDeductionPaymentsResource;

class OtherDeductionPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OtherDeductionAllList $request)
    {
        $validatedData = $request->validated();
        $main = OtherDeductionPayments::where("posting_status", PostingStatusType::POSTED)
        ->when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->whereHas('employee', function ($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->with(["otherdeduction", "employee"])
        ->orderBy("id", "DESC")
        ->paginate(15);
        return OtherDeductionPaymentsResource::collection($main)
        ->additional([
            'success' => true,
            'message' => 'Successfully fetched.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOtherDeductionPaymentsRequest $request)
    {
        $main = new OtherDeductionPayments();
        $main->fill($request->validated());
        $data = json_decode('{}');

        if (!$main->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = OtherDeductionPayments::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $main;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOtherDeductionPaymentsRequest $request, $id)
    {
        $main = OtherDeductionPayments::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $main->fill($request->validated());
            if ($main->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $main;
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
        $main = OtherDeductionPayments::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            if ($main->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $main;
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
