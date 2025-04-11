<?php

namespace App\Http\Controllers;

use App\Models\EmployeeUploads;
use App\Http\Requests\StoreEmployeeUploadsRequest;
use App\Http\Requests\UpdateEmployeeUploadsRequest;
use App\Http\Traits\UploadFileTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeUploadsController extends Controller
{
    use UploadFileTrait;
    public const EMPLOYEEDIR = "employee_folder/";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = EmployeeUploads::paginate(15);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeUploadsRequest $request)
    {
        $validated = $request->validated();
        $main = new EmployeeUploads();
        $main->fill($validated);
        $location = $validated["upload_type"] == "Documents" ? EmployeeUploads::DOCS_DIR : EmployeeUploads::MEMO_DIR;
        $main->file_location = $this->uploadFile($validated['file'], $location);
        if (!$main->save()) {
            return response()->json([
                "message" => "Save failed.",
                "success" => false,
            ], 400);
        }
        return response()->json([
            "message" => "Successfully save.",
            "success" => true,
            "data" => $main,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = EmployeeUploads::find($id);
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
    public function update(UpdateEmployeeUploadsRequest $request, $id)
    {
        $main = EmployeeUploads::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            $main->fill($request->validated());
            $hashmake = Hash::make('secret');
            $hashname = hash('sha256', $hashmake);
            if ($request->hasFile("file")) {
                $folders = explode("/", $main->file_location);
                array_pop($folders);
                Storage::deleteDirectory("public/" . implode("/", $folders));
                $file = $request->file('resume_attachment');
                $name = $file->getClientOriginalName();
                $file->storePubliclyAs(EmployeeUploadsController::EMPLOYEEDIR . $hashname, $name, 'public');
                $main->file_location = EmployeeUploadsController::EMPLOYEEDIR . $hashname . "/" . $name;
            }

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
        $main = EmployeeUploads::find($id);
        $data = json_decode('{}');
        if (!is_null($main)) {
            if ($main->delete()) {
                $folders = explode("/", $main->file_location);
                array_pop($folders);
                Storage::deleteDirectory("public/" . implode("/", $folders));
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
