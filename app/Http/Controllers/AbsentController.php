<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;

class AbsentController extends Controller
{
    public function getAbsenceThisMonth(Schedule $req, AttendanceLog $log)
    {
        $getemployeeschedule = $req->scheduleEmployeeThisMonth($req);
        $absenceEmployeeData = [];
        foreach ($getemployeeschedule as $key => $value) {
            $from = $value->startRecur;
            $to = $value->endRecur;
            $maxDays = $to->diffInWeekdays($from);
            $EmployeeAbsence = $log->getAttendance($log, $value->employee_id, $value->startRecur, $value->endRecur);
            $absenceEmployeeData[$key]["employee_name"] = $value->employee->fullname_last;
            $absenceEmployeeData[$key]["absences"] = $maxDays - $EmployeeAbsence;
            if ($EmployeeAbsence >= $maxDays) {
                $absenceEmployeeData[$key]["absences"] = $maxDays;
            }
        }
        $dataval = collect($absenceEmployeeData)->unique();
        return new JsonResponse([
            'success' => 'true',
            'message' => 'Successfully fetched.',
            'data' => $dataval
        ]);
    }

}
