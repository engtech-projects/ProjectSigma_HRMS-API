<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Http\Requests\StoresettingsRequest;
use App\Http\Requests\UpdatesettingsRequest;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Settings::paginate(config("app.pagination_per_page"));
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $settings;
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
    public function store(StoresettingsRequest $request)
    {
        //
        $settings = new Settings();
        $settings->fill($request->validated());
        $data = json_decode('{}');

        if (!$settings->save()) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
        $data->message = "Successfully save.";
        $data->success = true;
        $data->data = $settings;
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $settings = Settings::find($id);
        $data = json_decode('{}');
        if (!is_null($settings)) {
            $data->message = "Successfully fetch.";
            $data->success = true;
            $data->data = $settings;
            return response()->json($data);
        }
        $data->message = "No data found.";
        $data->success = false;
        return response()->json($data, 404);
    }

    public function updateSettings(Request $request)
    {
        $data = json_decode('{}');
        try {
            $a = array();
            foreach (json_decode($request->getContent(), true) as $x) {
                array_push($a, $x);
            }
            $settings = Settings::upsert(
                $a,
                [
                    'id'
                ]
            );
            $data->message = "Successfully update.";
            $data->success = true;
            return response()->json($data);
        } catch (\Throwable $th) {
            $data->message = "Update failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Settings $settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatesettingsRequest $request, $id)
    {
        $settings = Settings::find($id);
        $data = json_decode('{}');
        if (!is_null($settings)) {
            $settings->fill($request->validated());
            if ($settings->save()) {
                $data->message = "Successfully update.";
                $data->success = true;
                $data->data = $settings;
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
        $settings = Settings::find($id);
        $data = json_decode('{}');
        if (!is_null($settings)) {
            if ($settings->delete()) {
                $data->message = "Successfully delete.";
                $data->success = true;
                $data->data = $settings;
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
