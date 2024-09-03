<?php

namespace App\Http\Controllers;

use App\Enums\LoanPaymentsType;
use App\Http\Requests\CashAdvanceRequest;
use App\Http\Requests\OtherDeductionAllList;
use App\Models\OtherDeduction;
use App\Http\Requests\StoreOtherDeductionRequest;
use App\Http\Requests\UpdateOtherDeductionRequest;
use App\Http\Resources\OtherDeductionResource;
use App\Utils\PaginateResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OtherDeductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OtherDeductionAllList $request)
    {
        $validatedData = $request->validated();
        $data = OtherDeduction::when($request->has('employee_id'), function($query) use ($validatedData) {
            return $query->whereHas('employee', function($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->orderBy("created_at", "DESC")
        ->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'All Other Deductions fetched.',
            'data' => PaginateResourceCollection::paginate(OtherDeductionResource::collection($data)->collect()),
        ]);
    }

    public function ongoing(OtherDeductionAllList $request)
    {
        $validatedData = $request->validated();
        $data = OtherDeduction::when($request->has('employee_id'), function($query) use ($validatedData) {
            return $query->whereHas('employee', function($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->filter(function ($otherded) {
            return !$otherded->cashPaid();
        })
        ->values()
        ->all();

        return new JsonResponse([
            'success' => true,
            'message' => 'Ongoing Other Deductions fetched.',
            'data' => PaginateResourceCollection::paginate(OtherDeductionResource::collection($data)->collect()),
        ]);
    }

    public function paid(OtherDeductionAllList $request)
    {
        $validatedData = $request->validated();
        $data = OtherDeduction::when($request->has('employee_id'), function($query) use ($validatedData) {
            return $query->whereHas('employee', function($query2) use ($validatedData) {
                $query2->where('employee_id', $validatedData["employee_id"]);
            });
        })
        ->orderBy("created_at", "DESC")
        ->get()
        ->filter(function ($otherded) {
            return $otherded->cashPaid();
        })
        ->values()
        ->all();

        return new JsonResponse([
            'success' => true,
            'message' => 'Paid Other Deductions fetched.',
            'data' => PaginateResourceCollection::paginate(OtherDeductionResource::collection($data)->collect()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOtherDeductionRequest $request)
    {
        $data = json_decode('{}');
        try {
            DB::transaction(function () use ($request) {
                foreach ($request->employees as $key) {
                    $main = new OtherDeduction();
                    $main->fill($request->validated());
                    $main->employee_id = $key;
                    $main->save();
                }
            });
            $data->message = "Successfully save.";
            $data->success = true;
            return response()->json($data);
        } catch (\Throwable $th) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = OtherDeduction::with('employee')->find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = PaginateResourceCollection::paginate(OtherDeductionResource::collection($main)->collect());
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOtherDeductionRequest $request, $id)
    {
        $main = OtherDeduction::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $main->fill($request->validated());
            if ($main->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = new OtherDeductionResource($main);
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
        $main = OtherDeduction::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            if ($main->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = new OtherDeductionResource($main);
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

    public function cashAdvancePayment(OtherDeduction $oded, CashAdvanceRequest $request)
    {
        $valid = true;
        $msg = "";

        if ($oded->cashPaid()) {
            $valid = false;
            $msg = "Payment already paid.";
        } elseif ($oded->paymentWillOverpay($request->paymentAmount)) {
            $valid = false;
            $msg = "Payment will overpay.";
        } else {
            $oded->cashAdvance($request->paymentAmount, LoanPaymentsType::MANUAL->value);
            $valid = true;
            $msg = "Payment successfully.";
        }

        $oded->refresh();
        if ($valid) {
            return new JsonResponse([
                'success' => $valid,
                'message' => $msg,
                "data" => $oded
            ]);
        }
        return new JsonResponse([
            'success' => $valid,
            'message' => $msg,
            "data" => $oded
        ], 400);
    }
}
