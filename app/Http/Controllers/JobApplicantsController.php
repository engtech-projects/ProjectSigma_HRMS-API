<?php

namespace App\Http\Controllers;

use App\Models\JobApplicants;
use App\Http\Requests\StoreJobApplicantsRequest;
use App\Http\Requests\UpdateJobApplicantsRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Hash;

class JobApplicantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $main = JobApplicants::simplePaginate(15);
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
    public function store(StoreJobApplicantsRequest $request)
    {
        $main = new JobApplicants;
        $main->fill($request->validated());
        $data = json_decode('{}');

        $application_letter_attachmentfile = $request->file('application_letter_attachment');
        $resume_attachment = $request->file('resume_attachment');

        $hashmake = Hash::make('secret'); // Generate a unique, random name...
        $hashname = hash('sha256',$hashmake); // Remove slashes and periods

        $name1 = $resume_attachment->getClientOriginalName();
        $name2 = $application_letter_attachmentfile->getClientOriginalName();

        $path1 = Storage::putFileAs('public/application_letter_attachment/'.$hashname, $application_letter_attachmentfile, $name2);
        $path2 = Storage::putFileAs('public/resume_attachment/'.$hashname, $resume_attachment, $name1);

        $main->resume_attachment = $hashname."/".$name1;
        $main->application_letter_attachment = $hashname."/".$name2;

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
        $main = JobApplicants::find($id);
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
    public function edit(JobApplicants $jobApplicants)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobApplicantsRequest $request, $id)
    {
        $main = JobApplicants::find($id);
        $data = json_decode('{}');
        if (!is_null($main) ) {
            $main->fill($request->validated());

            if($request->hasFile("application_letter_attachment")){
                $check = JobApplicants::find($id);
                $file = $request->file('application_letter_attachment');
                $hashname = explode("/",$check->application_letter_attachment);
                $hashcode = $hashname[0];
                $name = $file->getClientOriginalName();
                $path = Storage::putFileAs('public/application_letter_attachment/'.$hashcode, $file, $name);
                $main->application_letter_attachment = $hashcode."/".$name;
            }

            if($request->hasFile("resume_attachment")){
                $check = JobApplicants::find($id);
                $file = $request->file('resume_attachment');
                $hashname = explode("/",$check->resume_attachment);
                $hashcode = $hashname[0];
                $name = $file->getClientOriginalName();
                $path = Storage::putFileAs('public/resume_attachment/'.$hashcode, $file, $name);
                $main->resume_attachment = $hashcode."/".$name;
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
        //
        $main = JobApplicants::find($id);
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
