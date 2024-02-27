<?php
namespace App\Http\Controllers;
use App\Enums\EmployeeAddressType;
use App\Http\Requests\BulkValidationRequest;
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
    const HEADER_KEYS = ['family_name',
        'first_name',
        'middle_name',
        'name_suffix',
        'nick_name',
        'pre_street',
        'pre_brgy',
        'pre_city',
        'pre_zip',
        'pre_province',
        'telephone_number',
        'mobile_number',
        'per_street',
        'per_brgy',
        'per_city',
        'per_zip',
        'per_province',
        'date_of_birth',
        'place_of_birth',
        'citizenship',
        'blood_type',
        'gender',
        'religion',
        'civil_status',
        'date_of_marriage',
        'height',
        'weight',
        'phic_number',
        'pagibig_number',
        'tin_number',
        'sss_number',
        'father_name',
        'mother_name',
        'spouse_name',
        'spouse_datebirth',
        'spouse_occupation',
        'spouse_contact_no',
        'childrens',
        'childrens_date_of_birth',
        'person_to_contact_name',
        'person_to_contact_street',
        'person_to_contact_brgy',
        'person_to_contact_city',
        'person_to_contact_zip',
        'person_to_province',
        'person_to_contact_no',
        'person_to_contact_relationship',
        'previous_hospitalization',
        'previous_operation',
        'current_undergoing_treatment',
        'convicted_crime',
        'dismissed_resigned',
        'pending_administrative',
        'name_of_relative_working_with',
        'relationship_of_relative_working_with',
        'position_of_relative_working_with',
        'elementary_name',
        'elementary_education',
        'elementary_degree_earned_of_school',
        'dates_of_school_elementary',
        'honor_of_school_elementary',
        'highschool_education',
        'name_of_school_highschool',
        'secondary_degree_earned_of_school',
        'dates_of_school_highschool',
        'honor_of_school_highschool',
        'college_education',
        'name_of_school_college',
        'college_degree_earned_of_school',
        'dates_of_school_college',
        'honor_of_school_college',
        'vocational_education',
        'name_of_school_vocational',
        'vocational_degree_earned_of_school',
        'dates_of_school_vocational',
        'honor_of_school_vocational',
        'master_thesis_name',
        'master_thesis_date',
        'doctorate_desertation_name',
        'doctorate_desertation_date',
        'professional_license_name',
        'professional_license_date',
        'reference_name',
        'reference_address',
        'reference_posiiton',
        'reference_contact_no',
        'employee_id',
        'company',
        'date_hired',
        'employment_status',
        'position',
        'section_program',
        'department',
        'division',
        'imidiate_supervisor'];
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
    public function bulkSave(BulkValidationRequest $request){
        $validatedData = $request->validated();
        $elementaryDates = [];
        $highSchoolDates = [];
        $collegeDates = [];
        $vocationalDates = [];
        foreach(json_decode($validatedData['employees_data'], true) as $data)
        {
            if($data['status'] == 'unduplicate')
            {
                //permanenet address
                $address[] = [
                    [
                        'street' => $data['pre_street'],
                        'brgy' => $data['pre_brgy'],
                        'city' => $data['pre_city'],
                        'zip' => $data['pre_zip'],
                        'province' => $data['pre_province'],
                        'type' => EmployeeAddressType::PRESENT,
                    ],
                    [
                        'street' => $data['per_street'],
                        'brgy' => $data['per_brgy'],
                        'city' => $data['per_city'],
                        'zip' => $data['per_zip'],
                        'province' => $data['per_province'],
                        'type' => EmployeeAddressType::PERMANENT,
                    ]
                ];




                $school = [
                    'elementary_name' => $data['elementary_name'],
                    'elementary_education' => $data['elementary_education'],
                    'elementary_degree_earned_of_school' => $data[''],
                    'elementary_period_attendance_to' => null,
                    'elementary_period_attendance_from' => null,
                    'elementary_year_graduated' => null,
                    'elementary_honors_received' => $data['honor_of_school_elementary'],
                    //seconday studies
                    'secondary_name' => $data[''],
                    'secondary_education' => $data[''],
                    'secondary_degree_earned_of_school' => $data[''],
                    'secondary_period_attendance_to' => null,
                    'secondary_period_attendance_from' => null,
                    'secondary_year_graduated' => null,
                    'secondary_honors_received' => $data['honor_of_school_highschool'],
                    //vocational studies
                    'vocationalcourse_name' => $data[''],
                    'vocationalcourse_education' => $data[''],
                    'vocationalcourse_degree_earned_of_school' => $data[''],
                    'vocationalcourse_period_attendance_to' => null,
                    'vocationalcourse_period_attendance_from' => null,
                    'vocationalcourse_year_graduated' => null,
                    'vocationalcourse_honors_received' => $data['honor_of_school_vocational'],
                    //college studies
                    'college_name' => $data[''],
                    'college_education' => $data[''],
                    'college_degree_earned_of_school' => $data[''],
                    'college_period_attendance_to' => null,
                    'college_period_attendance_from' => null,
                    'college_year_graduated' => null,
                    'college_honors_received' => $data['honor_of_school_college'],
                    //graduate studies (need to remove out of excel)
                    'graduatestudies_name' => null,
                    'graduatestudies_education' => null,
                    'graduatestudies_degree_earned_of_school' => null,
                    'graduatestudies_period_attendance_to' =>null,
                    'graduatestudies_period_attendance_from' => null,
                    'graduatestudies_year_graduated' => null,
                    'graduatestudies_honors_received' => $data['honor_of_school_college'],
                ];

                if($data['dates_of_school_elementary'])
                {
                    $elementaryDates = explode('-',$data['dates_of_school_elementary']);
                    $school['elementary_period_attendance_from'] = $elementaryDates[0];
                    $school['elementary_period_attendance_to'] = $elementaryDates[1];
                    $school['elementary_year_graduated'] = $elementaryDates[1];
                }
                if($data['dates_of_school_highschool'])
                {
                    $highSchoolDates = explode('-',$data['dates_of_school_highschool']);
                    $school['secondary_period_attendance_from'] = $highSchoolDates[0];
                    $school['secondary_period_attendance_to'] = $highSchoolDates[1];
                    $school['elementary_year_graduated'] = $elementaryDates[1];
                }
                if($data['dates_of_school_college'])
                {
                    $collegeDates = explode('-',$data['dates_of_school_college']);
                    $school['vocationalcourse_period_attendance_from'] = $vocationalDates[0];
                    $school['vocationalcourse_period_attendance_to'] = $vocationalDates[1];
                    $school['elementary_year_graduated'] = $elementaryDates[1];
                }
                if($data['dates_of_school_vocational'])
                {
                    $vocationalDates = explode('-',$data['dates_of_school_vocational']);
                    $school['college_period_attendance_from'] = $collegeDates[0];
                    $school['college_period_attendance_to'] = $collegeDates[1];
                    $school['elementary_year_graduated'] = $elementaryDates[1];
                }

                $employee = new Employee;
                $employee->fill($data)->save();
                $employee->employee_address()->create($address);
            }
        }
        return response()->json([
            'message' => 'Done save data',
            'data' => [],
        ]);
    }
}
