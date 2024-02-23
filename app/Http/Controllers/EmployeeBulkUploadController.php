<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreEmployeeBulkUpload;
use App\Models\CompanyEmployee;
use App\Models\Employee;
use App\Models\EmployeeRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

class EmployeeBulkUploadController extends Controller
{
    const START_ROW = 3;
    const HEADER_KEYS = ['family_name', 'first_name', 'middle_name', 'name_suffix', 'nick_name', 'pre_street', 'pre_brgy', 'pre_city', 'pre_zip', 'pre_province', 'telephone_number', 'mobile_number', 'per_street', 'per_brgy', 'per_city', 'per_zip', 'per_province', 'date_of_birth', 'place_of_birth', 'citizenship', 'blood_type', 'gender', 'religion', 'civil_status', 'date_of_marriage', 'height', 'weight', 'phic_number', 'pagibig_number', 'tin_number', 'sss_number', 'father_name', 'mother_name', 'spouse_name', 'spouse_datebirth', 'spouse_occupation', 'spouse_contact_no', 'childrens', 'childrens_date_of_birth', 'person_to_contact_name', 'person_to_contact_street', 'person_to_contact_brgy', 'person_to_contact_city', 'person_to_contact_zip', 'person_to_province', 'person_to_contact_no', 'person_to_contact_relationship', 'previous_hospitalization', 'previous_operation', 'current_undergoing_treatment', 'convicted_crime', 'dismissed_resigned', 'pending_administrative', 'name_of_relative_working_with', 'relationship_of_relative_working_with', 'position_of_relative_working_with', 'elementary', 'name_of_school_elementary', 'degree_earned_of_school_elementary', 'dates_of_school_elementary', 'honor_of_school_elementary', 'highschool', 'name_of_school_highschool', 'degree_earned_of_school_highschool', 'dates_of_school_highschool', 'honor_of_school_highschool', 'college', 'name_of_school_college', 'degree_earned_of_school_college', 'dates_of_school_college', 'honor_of_school_college', 'vocational', 'name_of_school_vocational', 'degree_earned_of_school_vocational', 'dates_of_school_vocational', 'honor_of_school_vocational', 'master_thesis_name', 'master_thesis_date', 'doctorate_desertation_name', 'doctorate_desertation_date', 'professional_license_name', 'professional_license_date', 'reference_name', 'reference_address', 'reference_posiiton', 'reference_contact_no', 'employee_id', 'company', 'date_hired', 'employment_status', 'position', 'section_program', 'department', 'division', 'imidiate_supervisor'];
    public function bulkUpload (Request $request) {
        // if file is not chosen, redirect back
        if(!request()->hasFile('employees-data')) {
            return response()->json([ 'message' => 'no excel file'], 422);
        }

        $file = $request->file('employees-data');
        $extension = strtolower($file->getClientOriginalExtension());
        // check extensions
        if (!in_array($extension, ['xls', 'xlsx']))
        {
            return response()->json([ 'message' => 'invalid file'], 422);
        }
        $extractedData = [];
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        $totalData = $worksheet->getHighestDataRow('A') - Self::START_ROW;
        $headerColumnKey = 'A'.(Self::START_ROW - 1).':CQ'.(Self::START_ROW - 1);
        $header = $worksheet->rangeToArray($headerColumnKey, null, true, true);
        for($x = 0; $x < $totalData; $x++) {

            $tempData = [];
            $columnKey = 'A'.(Self::START_ROW + $x).':CQ'.(Self::START_ROW + $x);
            $extractData = $worksheet->rangeToArray($columnKey, null, true, true);

            foreach($extractData as $data)
            {
                if($data[0])
                {
                    $employeeRecord = Employee::orWhere([
                        [
                            'family_name','=', $data[0]
                        ],
                        [
                            'middle_name','=', $data[2]
                        ],
                        [
                            'first_name','=', $data[1]
                        ],
                    ])->first();

                    if($employeeRecord)
                    {
                        $tempData['status'] = 'duplicate';
                    }else{
                        $tempData['status'] = 'unduplicate';
                    }

                    foreach(Self::HEADER_KEYS as $index => $value)
                    {
                        $tempData[$value] = $data[$index] ?? null;
                    }
                    $tempData['date_of_birth'] = !$tempData['date_of_birth'] || $tempData['date_of_birth'] === 'N/A' ? null :  $tempData['date_of_birth'];
                    $tempData['date_of_marriage'] = !$tempData['date_of_marriage'] || $tempData['date_of_marriage'] === 'N/A' ? null : $tempData['date_of_marriage'];
                    $tempData['spouse_datebirth'] = !$tempData['spouse_datebirth'] || $tempData['spouse_datebirth'] === 'N/A' ? null : $tempData['spouse_datebirth'];
                    $extractedData[] = $tempData;
                }
            }
        }
        return response()->json([
            'message' => 'Done extract data',
            'data' => $extractedData,
        ]);
    }
    public function bulkSave(StoreEmployeeBulkUpload $request){
        $validatedData = $request->validated();
        foreach(json_decode($validatedData['employees_data'], true) as $data)
        {
            if($data['status'] == 'unduplicate')
            {
                $employee = new Employee;
                $employee->fill($data)->save();
                $employee->company_employments()->create($data);
                $employee->employment_records()->create($data);


            }
        }
        return response()->json([
            'message' => 'Done save data',
            'data' => [],
        ]);
    }
}
