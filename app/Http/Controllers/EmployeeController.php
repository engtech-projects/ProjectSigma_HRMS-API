<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\SearchStudentRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $main = Employee::simplePaginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    public function search(SearchStudentRequest $request){
        $validatedData = $request->validated();
        $searchKey = $validatedData["key"];
        $main =
        Employee::where(function ($q) use ($searchKey) {
            // $q->orWhere('first_name', 'like', "%{$searchKey}%")
            //     ->orWhere('family_name', 'like', "%{$searchKey}%")
            //     ->orWhere('middle_name', 'like', "%{$searchKey}%");
        })->orWhere(DB::raw("CONCAT(family_name, ', ', first_name, ', ', middle_name)"), 'LIKE', $searchKey."%")
        ->orWhere(DB::raw("CONCAT(first_name, ', ', middle_name, ', ', family_name)"), 'LIKE', $searchKey."%")
        ->limit(25)->orderBy('family_name')->get();
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    public function get()
    {
        $main = Employee::with("company_employments","employment_records")->get();
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
    public function store(StoreEmployeeRequest $request)
    {
        //
        $main = new Employee;
        $main->fill($request->validated());
        $data = json_decode('{}');

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
        //
        $main = Employee::with("company_employments","employment_records")->get()->find($id);
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
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request,  $id)
    {
        //
        $main = Employee::find($id);
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
        //
        $main = Employee::find($id);
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
