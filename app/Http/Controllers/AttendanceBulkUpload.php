<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkAttendanceRequest;
use App\Models\AttendanceLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AttendanceBulkUpload extends Controller
{
    public const PROJECT_ROW = 1;
    public const START_ROW = 3;
    public const HEADER_KEYS = [
        'employee_id',
        'first_name',
        'middle_name',
        'family_name',
        'date',
        'time_in',
        'time_out',
    ];

    public function bulkUpload(Request $request)
    {
        // if file is not chosen, redirect back
        if (!request()->hasFile('attendance_logs')) {
            return response()->json(['message' => 'no excel file'], 422);
        }

        $file = $request->file('attendance_logs');
        $extension = strtolower($file->getClientOriginalExtension());
        // check extensions
        if (!in_array($extension, ['xls', 'xlsx'])) {
            return response()->json(['message' => 'invalid file'], 422);
        }
        $extractedData = [];
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $totalData = $worksheet->getHighestDataRow('A') - self::START_ROW;
        $thisKey = 'A' . (self::PROJECT_ROW + 0) . ':D' . (self::PROJECT_ROW + 0);
        $valData = $worksheet->rangeToArray($thisKey, null, true, true);
        for ($x = 0; $x <= $totalData; $x++) {
            $tempData = [];
            $columnKey = 'A' . (self::START_ROW + $x) . ':G' . (self::START_ROW + $x);
            $extractData = $worksheet->rangeToArray($columnKey, null, true, true);
            foreach ($extractData as $data) {
                if ($data[0]) {
                    foreach (self::HEADER_KEYS as $index => $value) {
                        $valdata =  $data[$index];
                        if ($index == 4) {
                            $valdata = date('F j, Y', strtotime($data[$index]));
                        }
                        $tempData[$value] = $valdata;
                    }
                    $tempData['project'] = $valData[0][1];
                    $tempData['project_id'] = $valData[0][3];
                    $extractedData[] = $tempData;
                }
            }
        }
        return response()->json([
            'message' => 'Done extract data',
            'data' => $extractedData,
        ]);
    }

    public function bulkSave(BulkAttendanceRequest $request)
    {
        $validatedData = $request->validated();
        try {
            DB::transaction(function () use ($validatedData) {
                foreach ($validatedData['attendance_data'] as $data) {
                    $attlogs = new AttendanceLog();
                    if ($data['time_in']) {
                        $date = date('Y-m-d', strtotime($data["date"]));
                        $time = date('H:i:s', strtotime($data["time_in"]));
                        $attlogs->fill($data);
                        $employee_id = (int)$data["employee_id"];
                        $dept_id = Employee::with('current_employment')->find($employee_id)->first()->current_employment->department_id;
                        $attlogs["employee_id"] = $employee_id;
                        $attlogs["department_id"] = $dept_id;
                        $attlogs["date"] = $date;
                        $attlogs["time"] = $time;
                        $attlogs["log_type"] = "In";
                        $attlogs->save();
                    }

                    if ($data['time_out']) {
                        $date = date('Y-m-d', strtotime($data["date"]));
                        $time = date('H:i:s', strtotime($data["time_in"]));
                        $attlogs->fill($data);
                        $employee_id = (int)$data["employee_id"];
                        $dept_id = Employee::with('current_employment')->find($employee_id)->first()->current_employment->department_id;
                        $attlogs["employee_id"] = $employee_id;
                        $attlogs["department_id"] = $dept_id;
                        $attlogs["date"] = $date;
                        $attlogs["time"] = $time;
                        $attlogs["log_type"] = "Out";
                        $attlogs->save();
                    }
                }
            });
            return response()->json([
                'message' => 'Successfully save attendance data.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed save attendance data.',
            ]);
        }
    }

    public function getTemplate()
    {
        return Storage::download('public/template/attendance_logs.xlsx');
    }
}
