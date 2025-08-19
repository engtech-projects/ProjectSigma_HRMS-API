<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterByScheduleGroupType;
use App\Http\Requests\ScheduleFilterRequest;
use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleDetailedResource;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ScheduleFilterRequest $request)
    {
        $validatedData = $request->validated();
        $startDate = $request->has('start_date') ? Carbon::parse($validatedData['start_date']) : Carbon::now()->subMonth()->startOfMonth()->startOfDay();
        $endDate = $request->has('end_date') ? Carbon::parse($validatedData['end_date']) : Carbon::now()->addMonth()->endOfMonth()->endOfDay();
        $data = Schedule::when($request->has('department_id'), function ($query) use ($validatedData) {
                return $query->where('department_id', $validatedData['department_id'])->with('department');
            })
            ->when($request->has('employee_id'), function ($query) use ($validatedData) {
                return $query->where('employee_id', $validatedData['employee_id'])->with('employee');
            })
            ->when($request->has('project_id'), function ($query) use ($validatedData) {
                return $query->where('project_id', $validatedData['project_id'])->with('project');
            })
            ->betweenDates($startDate, $endDate)
           ->get();
        return ScheduleDetailedResource::collection($data)->additional([
            'message' => 'Successfully fetched schedules.',
            'success' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        $main = new Schedule();
        $validatedData = $request->validated();
        if ($validatedData['scheduleType'] == Schedule::TYPE_IRREGULAR) {
            $validatedData['endRecur'] = $request->input('startRecur');
            $validatedData['endRecur'] = (new Carbon($validatedData['endRecur']))->addDay();
        }
        $main->fill($validatedData);
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $main = Schedule::with("department", "employee")->find($id);
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
     * Display data by groupType.
     */
    public function getGroupType(FilterByScheduleGroupType $request)
    {
        $main = Schedule::with("department", "employee")->where("groupType", $request);
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
    public function update(UpdateScheduleRequest $request, $id)
    {
        $main = Schedule::find($id);
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
        $main = Schedule::find($id);
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
}
