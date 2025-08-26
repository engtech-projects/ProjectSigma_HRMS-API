<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterByScheduleGroupType;
use App\Http\Requests\ScheduleFilterRequest;
use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleDetailedResource;
use Carbon\Carbon;
use Illuminate\Http\Response;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ScheduleFilterRequest $request)
    {
        $validatedData = $request->validated();
        $startDate = $request->filled('start_date')
            ? Carbon::parse($validatedData['start_date'])->startOfDay()
            : Carbon::now()->subMonth()->startOfMonth()->startOfDay();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($validatedData['end_date'])->endOfDay()
            : Carbon::now()->addMonth()->endOfMonth()->endOfDay();
        $relations = [
            $request->filled('department_id') ? 'department' : null,
            $request->filled('employee_id') ? 'employee' : null,
            $request->filled('project_id') ? 'project' : null
        ];
        $data = Schedule::when($request->filled('department_id'), function ($query) use ($validatedData) {
                $query->where('department_id', $validatedData['department_id']);
            })
            ->when($request->filled('employee_id'), function ($query) use ($validatedData) {
                $query->where('employee_id', $validatedData['employee_id']);
            })
            ->when($request->filled('project_id'), function ($query) use ($validatedData) {
                $query->where('project_id', $validatedData['project_id']);
            })
            ->with($relations)
            ->betweenDates($startDate, $endDate)
           ->get();
        return ScheduleDetailedResource::collection($data)->additional([
            'message' => 'Successfully fetched schedules.',
            'success' => true,
            'filter_used' => $validatedData,
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
            $validatedData['endRecur'] = Carbon::parse($validatedData['startRecur'])->addDay();
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
    public function show(Schedule $schedule)
    {
        $schedule->load("department", "employee");
        return response()->json([
            'message' => 'Successfully fetch.',
            'success' => true,
            'data' => $schedule
        ]);
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
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $validatedData = $request->validated();
        if ($validatedData['scheduleType'] == Schedule::TYPE_IRREGULAR) {
            $validatedData['endRecur'] = Carbon::parse($validatedData['startRecur'])->addDay();
        }
        $schedule->fill($validatedData);
        if ($schedule->save()) {
            return response()->json([
                'message' => 'Successfully update.',
                'success' => true,
                'data' => $schedule
            ]);
        }
        return response()->json([
            'message' => 'Failed update.',
            'success' => false,
        ], Response::HTTP_BAD_REQUEST);
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
