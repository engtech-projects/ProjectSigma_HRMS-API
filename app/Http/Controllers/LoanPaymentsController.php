<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoansAllRequest;
use App\Http\Resources\LoanPaymentResource;
use App\Models\LoanPayments;
use App\Http\Requests\StoreLoanPaymentsRequest;
use App\Http\Requests\UpdateLoanPaymentsRequest;

class LoanPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LoansAllRequest $request)
    {
        $validatedData = $request->validated();
        $data = LoanPayments::when($request->has('employee_id'), function ($query) use ($validatedData) {
            return $query->whereHas('employee', function ($query2) use ($validatedData) {
                return $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->with(['employee','loan'])
        ->orderBy("created_at", "DESC")
        ->paginate(config("app.pagination_per_page"));
        return LoanPaymentResource::collection($data)
        ->additional([
            'success' => true,
            'message' => 'Loan Payments fetched.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoanPaymentsRequest $request)
    {
        $main = new LoanPayments();
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
        $main = LoanPayments::find($id);
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
    public function update(UpdateLoanPaymentsRequest $request, $id)
    {
        $main = LoanPayments::find($id);
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
        $main = LoanPayments::find($id);
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
