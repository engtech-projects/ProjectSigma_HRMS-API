<?php

namespace App\Http\Controllers;

use App\Models\Approvals;
use App\Models\Users;
use App\Http\Requests\StoreApprovalsRequest;
use App\Http\Requests\UpdateApprovalsRequest;
use Illuminate\Http\Request;

class ApprovalsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = Approvals::simplePaginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }


    public function get($request)
    {
        $main = Approvals::where("form","=",$request)->first();
        if(!is_null($main)){
            $fetchdata = $main->approvals;
            $a = json_decode($fetchdata);
            $c = 0;
            foreach($a as $x){

                if($x->user_id==null){
                    $data = json_decode('{}');
                    $data->message = "Failed fetch.";
                    $data->success = false;
                    $data->data = $main;
                    return response()->json($data);
                    break;
                }

                $fetchuser = Users::find($x->user_id);
                $a[$c]->name = $fetchuser->name;
                $c+=1;
            }
            $fetchdata = $a;
        }
        $main->approvals = $fetchdata;
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
    public function store(StoreApprovalsRequest $request)
    {
        $main = new Approvals;
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = Approvals::find($id);
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
    public function edit(Approvals $approvals)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApprovalsRequest $request, $id)
    {
        $main = Approvals::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $main->fill($request->validated());
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
        $main = Approvals::find($id);
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
