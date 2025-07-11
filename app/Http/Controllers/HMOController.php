<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHMOMembersRequest;
use App\Models\HMO;
use App\Http\Requests\StoreHMORequest;
use App\Http\Requests\UpdateHMORequest;
use Illuminate\Support\Facades\DB;

class HMOController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $main = HMO::with("hmoMembers")->paginate(config("app.pagination_per_page"));
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHMORequest $request)
    {
        $main = new HMO();
        $main->fill($request->validated());
        $data = json_decode('{}');
        try {
            DB::transaction(function () use ($main, $request) {
                $main->save();
                $main->savehmoMembers()->createMany(
                    $request->hmo_members
                );
            });
            $data->message = "Successfully save.";
            $data->success = true;
            $data->data = $main;
            return response()->json($data);
        } catch (\Exception $th) {
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
        $main = HMO::with("hmoMembers")->find($id);
        $data = json_decode('{}');
        $data->message = "Successfully fetch.";
        $data->success = true;
        $data->data = $main;
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHMORequest $request, $id)
    {
        $data = json_decode('{}');
        $main = HMO::find($id);

        if (is_null($main)) {
            $data->message = "Failed update.";
            $data->success = false;
            return response()->json($data, 404);
        }

        try {
            $main->fill($request->validated());
            DB::transaction(function () use ($main, $request) {
                $main->save();
                foreach ($request->hmo_members as $key) {
                    $main->savehmoMembers()->upsert(
                        $key,
                        uniqueBy: ['id']
                    );
                }
            });
            $data->message = "Successfully save.";
            $data->success = true;
            $data->data = $main;
            return response()->json($data);
        } catch (\Exception $th) {
            $data->message = "Save failed.";
            $data->success = false;
            return response()->json($data, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $main = HMO::find($id);
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

    public function storeHmoMembers(StoreHMOMembersRequest $request)
    {
        if ($request->validated()) {
            return true;
        }
        return false;
    }
}
