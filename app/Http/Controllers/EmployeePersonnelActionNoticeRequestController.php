<?php

namespace App\Http\Controllers;

use App\Models\EmployeePersonnelActionNoticeRequest;
use App\Http\Requests\StoreEmployeePersonnelActionNoticeRequestRequest;
use App\Http\Requests\UpdateEmployeePersonnelActionNoticeRequestRequest;
use App\Models\Employee;
use App\Models\InternalWorkExperience;
use App\Models\JobApplicants;
use App\Models\ManpowerRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeePersonnelActionNoticeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $main = EmployeePersonnelActionNoticeRequest::paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
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
    public function store(StoreEmployeePersonnelActionNoticeRequestRequest $request)
    {
        //
        $main = new EmployeePersonnelActionNoticeRequest;
        $main->fill($request->validated());
        $data = json_decode('{}');
        $main->approvals = json_encode($request->approvals);
        if(!$main->save()){
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    public function get_panrequest()
    {
        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::where("created_by","=",$id)->get();
        $data = json_decode('{}');
        if (!is_null($main) ) {
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
     * Show can view all pan request to be approved by logged in user (same login in manpower request)
     */
    public function get_approve()
    {
        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::where("created_by",'=',$id)->get();
        $newdata = json_decode('{}');
        foreach ($main as $key => $x) {
            $new_approvals = [];
            foreach(json_decode($x->approvals) as $data){
                $type =  gettype($data);
                if($type=="object"){
                    $one_approval = $data;
                    $approval_id = $one_approval->user_id;
                    $approval_status = $one_approval->status;
                    if($approval_id==$id && $approval_status=="Pending"){
                        array_push($new_approvals,$one_approval);
                        break;
                    }
                    if($approval_status=="Denied"){
                        break;
                    }
                }else if($type=="array"){
                    $many_approval=json_decode($data);
                    foreach($many_approval as $one_approval){
                        $approval_id = $one_approval->user_id;
                        $approval_status = $one_approval->status;
                        if($approval_status=="Denied"){
                            break;
                        }
                        if($approval_id==$id && $approval_status=="Pending"){
                            array_push($new_approvals,$one_approval);
                            break;
                        }
                    }
                }
            }
            $main[$key]->approvals = $new_approvals;
        }
        $newdata->message = "Successfully fetch.";
        $newdata->success = true;
        $newdata->data = $main;
        return response()->json($newdata);
    }

    // logged in can approve pan request(if he is the current approval)
    public function approve_approval($request)
    {
        $id = Auth::user()->id;
        $main = EmployeePersonnelActionNoticeRequest::where([
            ["created_by",'=',$id],
            ["id",'=',$request]
        ])->first();

        $newdata = json_decode('{}');
        $newdata->success = false;
        $newdata->message = "Failed approved.";

        if(!$main){
            $newdata->message = "No data found.";
            return response()->json($newdata);
        }

        $approval = json_decode($main->approvals);
        foreach($approval as $index => $key){
            $data = json_decode($key);
            $type =  gettype($data);
            $approval_id = 0;
            $approval_status = "";

            if($type=="object"){
                $approval_id = $data->user_id;
                $approval_status = $data->status;
            }elseif($type=="array"){
                $approval_id = $data[$index]->user_id;
                $approval_status = $data[$index]->status;
            }

            if($approval_status=="Denied"){
                break;
            }

            if($approval_id!=$id && $approval_status=="Pending"){
                break;
            }

            if($approval_id==$id && $approval_status=="Pending"){
                if($type=="object"){
                    $data->date_approved = Carbon::now();
                    $data->status = "Approved";
                }elseif($type=="array"){
                    $data[$index]->date_approved = Carbon::now();
                    $data[$index]->status = "Approved";
                }
                $approval[$index] = json_encode($data);
                $newdata->success = true;
                $newdata->message = "Successfully approved.";
                break;
            }
        }

        if($newdata->success){
            $main->approvals = json_encode($approval);
            if($main->save()){
                $newdata->data = $main;
                return response()->json($newdata);
            }
        }else{
            $main->approvals = [];
        }

        $newdata->data = $main;
        return response()->json($newdata);
    }

    function hire_approved ($request) {
        $employee = new Employee();
        $employee_internalwork = new InternalWorkExperience();
        $employee->fill($request)->save();
        $employee->company_employments()->fill($request)->save();
        // $employee->employment_records()->create($request);
        $employee->employee_address()->fill($request)->save();
        $employee->employee_address()->fill($request)->save();
        $employee->employee_affiliation()->fill($request)->save();
        $employee->employee_education()->fill($request)->save();
        $employee_internalwork->fill($request)->save();
        // $employee->employee_eligibility()->create($request);
        // $ja = JobApplicants::where("")
        // $manpower = ManpowerRequest::where("")
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $main = EmployeePersonnelActionNoticeRequest::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
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
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeePersonnelActionNoticeRequest $employeePersonnelActionNoticeRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeePersonnelActionNoticeRequestRequest $request,  $id)
    {
        //
        $main = EmployeePersonnelActionNoticeRequest::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $main->fill($request->validated());
            $main->approvals = json_encode($request->approvals);
            if($main->save()){
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
    public function destroy( $id)
    {
        //
        $main = EmployeePersonnelActionNoticeRequest::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            if($main->delete()){
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $main;
                return response()->json($data);
            }
            $data->message = "Failed delete.";
            $data->success = false;
            return response()->json($data,400);
        }
        $data->message = "Failed delete.";
        $data->success = false;
        return response()->json($data,404);
    }
}
