<?php

namespace App\Http\Controllers;

use App\Models\ManpowerRequest;
use App\Http\Requests\StoreManpowerRequestRequest;
use App\Http\Requests\UpdateManpowerRequestRequest;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManpowerRequestController extends Controller
{
    const JDDIR = "job_description/";
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = ManpowerRequest::paginate(15);
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
     * Show all requests made by current user.
     */
    public function get()
    {
        $id = Auth::user()->id;
        $main = ManpowerRequest::where("requested_by",'=',$id)->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Show all requests to be approved/reviewed by current user
     */
    public function get_approve()
    {
        $id = Auth::user()->id;
        $main = ManpowerRequest::where("requested_by",'=',$id)->get();
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

    public function approve_approval($request)
    {
        $id = Auth::user()->id;
        $main = ManpowerRequest::where([
            ["requested_by",'=',$id],
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

    public function deny_approval($request)
    {
        $id = Auth::user()->id;
        $main = ManpowerRequest::where([
            ["requested_by",'=',$id],
            ["id",'=',$request]
        ])->first();

        $newdata = json_decode('{}');
        $newdata->success = false;
        $newdata->message = "Failed denied.";

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
                // $data->date_approved = Carbon::now();
                if($type=="object"){
                    $data->status = "Denied";
                }elseif($type=="array"){
                    $data[$index]->status = "Denied";
                }
                $approval[$index] = json_encode($data);
                $newdata->success = true;
                $newdata->message = "Successfully denied.";
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManpowerRequestRequest $request)
    {
        $main = new ManpowerRequest;
        $main->fill($request->validated());
        $data = json_decode('{}');
        $main->approvals = json_encode($request->approvals);
        $file = $request->file('job_description_attachment');
        $hashmake = Hash::make('secret');
        $hashname = hash('sha256',$hashmake);
        $name = $file->getClientOriginalName();
        $path = $file->storePubliclyAs(ManpowerRequestController::JDDIR.$hashname, $name,'public');
        $main->job_description_attachment = ManpowerRequestController::JDDIR.$hashname."/".$name;
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = ManpowerRequest::find($id);
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
    public function edit(ManpowerRequest $manpowerRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManpowerRequestRequest $request, $id)
    {
        $main = ManpowerRequest::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $a = explode("/", $main->job_description_attachment);
            $main->fill($request->validated());
            if($request->hasFile("job_description_attachment")){
                $check = ManpowerRequest::find($id);
                $file = $request->file('job_description_attachment');
                $hashmake = Hash::make('secret');
                $hashname = hash('sha256',$hashmake);
                $name = $file->getClientOriginalName();
                $path = $file->storePubliclyAs(ManpowerRequestController::JDDIR.$hashname, $name,'public');
                Storage::deleteDirectory("public/".$a[0]."/".$a[1]);
                $main->job_description_attachment = ManpowerRequestController::JDDIR.$hashname."/".$name;
            }

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
    public function destroy($id)
    {
        $main = ManpowerRequest::find($id);
        $a = explode("/", $main->job_description_attachment);
        Storage::deleteDirectory("public/".$a[0]."/".$a[1]);
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
