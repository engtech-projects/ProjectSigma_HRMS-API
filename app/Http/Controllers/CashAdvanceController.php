<?php

namespace App\Http\Controllers;

use App\Enums\LoanPaymentsType;
use App\Enums\RequestStatusType;
use App\Models\CashAdvance;
use App\Http\Requests\StoreCashAdvanceRequest;
use App\Http\Requests\UpdateCashAdvanceRequest;
use App\Http\Requests\CashAdvanceRequest;
use App\Http\Resources\CashAdvanceResource;
use App\Http\Services\CashAdvanceService;
use Illuminate\Http\JsonResponse;

class CashAdvanceController extends Controller
{
    protected $RequestService;
    public function __construct(CashAdvanceService $RequestService)
    {
        $this->RequestService = $RequestService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = CashAdvance::with("employee", "department", "project", "cashAdvancePayments")->paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCashAdvanceRequest $request)
    {
        $main = new CashAdvance();
        $main->fill($request->validated());
        $main->request_status = RequestStatusType::PENDING;
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

    public function cashAdvancePayment(CashAdvance $cash, CashAdvanceRequest $request)
    {
        $valid = true;
        $msg = "";

        if ($cash->cashPaid()) {
            $valid = false;
            $msg = "Payment already paid.";
        } elseif ($cash->paymentWillOverpay($request->paymentAmount)) {
            $valid = false;
            $msg = "Payment will overpay.";
        } else {
            $cash->cashAdvance($request->paymentAmount, LoanPaymentsType::MANUAL->value);
            $valid = true;
            $msg = "Payment successfully.";
        }

        $cash->refresh();

        $data = $cash->with('cashAdvancePayments')->get();

        return new JsonResponse([
            'success' => $valid,
            'message' => $msg,
            "data" => $data
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = CashAdvance::with("employee", "department", "project")->find($id);
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
    public function update(UpdateCashAdvanceRequest $request, $id)
    {
        $main = CashAdvance::find($id);
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
        $main = CashAdvance::find($id);
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

    public function myRequests()
    {
        $myRequest = $this->RequestService->getMyRequest();
        if ($myRequest->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Leave Request fetched.',
            'data' => CashAdvanceResource::collection($myRequest)
        ]);
    }

    /**
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function myApprovals()
    {
        $myApproval = $this->RequestService->getMyApprovals();
        if ($myApproval->isEmpty()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No data found.',
            ], JsonResponse::HTTP_OK);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Cash Advance Request fetched.',
            'data' => CashAdvanceResource::collection($myApproval)
        ]);
    }
}
