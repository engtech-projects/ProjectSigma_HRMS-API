<?php

namespace App\Http\Controllers;

use App\Enums\EmployeeAddressType;
use App\Enums\EmployeeEducationType;
use App\Enums\EmployeeRelatedPersonType;
use App\Enums\EmployeeStudiesType;
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
    public const START_ROW = 3;
    public const HEADER_KEYS = [
        'family_name',
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
        'person_to_contact_province',
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
        'secondary_name',
        'secondary_education',
        'secondary_degree_earned_of_school',
        'dates_of_school_highschool',
        'honor_of_school_highschool',
        'college_name',
        'college_education',
        'college_degree_earned_of_school',
        'dates_of_school_college',
        'honor_of_school_college',
        'vocationalcourse_name',
        'vocationalcourse_education',
        'vocationalcourse_degree_earned_of_school',
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
        'employeedisplay_id',
        'company',
        'date_hired',
        'employment_status',
        'position',
        'section_program',
        'department',
        'division',
        'imidiate_supervisor'
    ];
    public function bulkUpload(Request $request)
    {
        // if file is not chosen, redirect back
        if (!request()->hasFile('employees-data')) {
            return response()->json(['message' => 'no excel file'], 422);
        }

        $file = $request->file('employees-data');
        $extension = strtolower($file->getClientOriginalExtension());
        // check extensions
        if (!in_array($extension, ['xls', 'xlsx'])) {
            return response()->json(['message' => 'invalid file'], 422);
        }
        $extractedData = [];
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        $totalData = $worksheet->getHighestDataRow('A') - self::START_ROW;
        $headerColumnKey = 'A' . (self::START_ROW - 1) . ':CQ' . (self::START_ROW - 1);
        $header = $worksheet->rangeToArray($headerColumnKey, null, true, true);
        for ($x = 0; $x < $totalData; $x++) {
            $tempData = [];
            $columnKey = 'A' . (self::START_ROW + $x) . ':CQ' . (self::START_ROW + $x);
            $extractData = $worksheet->rangeToArray($columnKey, null, true, true);

            foreach ($extractData as $data) {
                if ($data[0]) {
                    $employeeRecord = Employee::orWhere([
                        [
                            'family_name',
                            '=',
                            $data[0]
                        ],
                        [
                            'middle_name',
                            '=',
                            $data[2]
                        ],
                        [
                            'first_name',
                            '=',
                            $data[1]
                        ],
                    ])->first();
                    if ($employeeRecord) {
                        $tempData['_status'] = 'duplicate';
                    } else {
                        $tempData['_status'] = 'unduplicate';
                    }
                    foreach (self::HEADER_KEYS as $index => $value) {
                        $tempData[$value] = $data[$index];
                    }
                    $tempData['date_of_birth'] = !$tempData['date_of_birth'] ||
                        $tempData['date_of_birth'] === 'N/A' ?
                        null : $tempData['date_of_birth'];
                    $tempData['date_of_marriage'] = !$tempData['date_of_marriage'] ||
                        $tempData['date_of_marriage'] === 'N/A' ?
                        null : $tempData['date_of_marriage'];
                    $tempData['spouse_datebirth'] = !$tempData['spouse_datebirth'] ||
                        $tempData['spouse_datebirth'] === 'N/A' ?
                        null : $tempData['spouse_datebirth'];
                    $extractedData[] = $tempData;
                }
            }
        }
        return response()->json([
            'message' => 'Done extract data',
            'data' => $extractedData,
        ]);
    }
    public function bulkSave(BulkValidationRequest $request)
    {
        $validatedData = $request->validated();
        $elementaryDates = [];
        $highSchoolDates = [];
        $education = [];
        $collegeDates = [];
        $vocationalDates = [];
        $studies = [];
        $employeeRelatedPerson = [];
        foreach (json_decode($validatedData['employees_data'], true) as $data) {
            if ($data['status'] == 'unduplicate') {
                //insert
                $employee = new Employee();
                $employee->fill($data)->save();

                if ($data['dates_of_school_elementary']) {
                    $elementaryDates = explode('-', $data['dates_of_school_elementary']);
                    if ($elementaryDates && count($elementaryDates) > 1) {
                        $education['elementary_period_attendance_from'] = $elementaryDates[0] ?? 'N/A';
                        $education['elementary_period_attendance_to'] = $elementaryDates[1] ?? 'N/A';
                        $education['elementary_year_graduated'] = $elementaryDates[1] ?? 'N/A';
                    }
                }
                if ($data['dates_of_school_highschool']) {
                    $highSchoolDates = explode('-', $data['dates_of_school_highschool']);
                    if ($highSchoolDates && count($highSchoolDates) > 1) {
                        $education['secondary_period_attendance_from'] = $highSchoolDates[0] ?? 'N/A';
                        $education['secondary_period_attendance_to'] = $highSchoolDates[1] ?? 'N/A';
                        $education['secondary_year_graduated'] = $highSchoolDates[1] ?? 'N/A';
                    }
                }
                if ($data['dates_of_school_college']) {
                    $collegeDates = explode('-', $data['dates_of_school_college']);
                    if ($collegeDates && count($collegeDates) > 1) {
                        $education['college_period_attendance_from'] = $collegeDates[0] ?? 'N/A';
                        $education['college_period_attendance_to'] = $collegeDates[1] ?? 'N/A';
                        $education['college_year_graduated'] = $collegeDates[1] ?? 'N/A';
                    }
                }
                if ($data['dates_of_school_vocational']) {
                    $vocationalDates = explode('-', $data['dates_of_school_vocational']);
                    if ($vocationalDates && count($vocationalDates) > 1) {
                        $education['vocationalcourse_period_attendance_from'] = $vocationalDates[0] ?? 'N/A';
                        $education['vocationalcourse_period_attendance_to'] = $vocationalDates[1] ?? 'N/A';
                        $education['vocationalcourse_year_graduated'] = $vocationalDates[1] ?? 'N/A';
                    }
                }
                if ($data['childrens'] && $data['childrens'] != 'N/A') {
                    $children = explode(',', $data['childrens']);
                    if ($children) {
                        foreach ($children as $child) {
                            $childrenInformation = explode('/', $child);
                            $employeeRelatedPerson[] = [
                                'relationship',
                                'type' => EmployeeRelatedPersonType::CHILD,
                                'relationship' => EmployeeRelatedPersonType::CHILD,
                                'name' => $childrenInformation[0] ?? 'N/A',
                                'date_of_birth' => $childrenInformation[1] ?? null,
                                'street' => 'N/A',
                                'brgy' => 'N/A',
                                'city' => 'N/A',
                                'zip' => 'N/A',
                                'province' => 'N/A',
                                'occupation' => 'N/A',
                                'contact_no' => 'N/A',
                            ];
                        }
                    }
                }

                //permanenet address
                $address_pre = [
                    'street' => $data['pre_street'] ?? 'N/A',
                    'brgy' => $data['pre_brgy'] ?? 'N/A',
                    'city' => $data['pre_city'] ?? 'N/A',
                    'zip' => $data['pre_zip'] ?? 'N/A',
                    'province' => $data['pre_province'] ?? 'N/A',
                    'type' => EmployeeAddressType::PRESENT,
                ];
                $address_per = [
                    'street' => $data['per_street'] ?? 'N/A',
                    'brgy' => $data['per_brgy'] ?? 'N/A',
                    'city' => $data['per_city'] ?? 'N/A',
                    'zip' => $data['per_zip'] ?? 'N/A',
                    'province' => $data['per_province'] ?? 'N/A',
                    'type' => EmployeeAddressType::PERMANENT,
                ];

                //affiliation information
                $affiliation = [
                    'club_organization_name' => 'N/A',
                    'membership_type' => 'N/A',
                    'status' => 'N/A',
                    'membership_exp_date' => null,
                ];

                //employee record information
                $employeeRecord = [
                    'date_to' => 'N/A',
                    'date_from' => 'N/A',
                    'position_title' => 'N/A',
                    'company_name' => 'N/A',
                    'monthly_salary' => 'N/A',
                    'status_of_appointment' => 'N/A',
                ];

                //elementary
                $employeeEducation[] = [
                    'honors_received' => $data['honor_of_school_elementary'] ?? 'N/A',
                    'degree_earned_of_school' => $data['elementary_degree_earned_of_school'] ?? 'N/A',
                    'year_graduated' => 'N/A',
                    'period_attendance_from' => 'N/A',
                    'period_attendance_to' => 'N/A',
                    'education' => $data['elementary_education'] ?? 'N/A',
                    'type' => EmployeeEducationType::ELEMENTARY,
                    'name' =>  $data['elementary_name'] ?? 'N/A',
                ];

                //secondary
                $employeeEducation[] = [
                    'honors_received' => $data['honor_of_school_highschool'] ?? 'N/A',
                    'degree_earned_of_school' => $data['secondary_degree_earned_of_school'] ?? 'N/A',
                    'year_graduated' => 'N/A',
                    'period_attendance_from' => 'N/A',
                    'period_attendance_to' => 'N/A',
                    'education' => $data['secondary_education'] ?? 'N/A',
                    'type' => EmployeeEducationType::SECONDARY,
                    'name' =>  $data['secondary_name'] ?? 'N/A',
                ];

                //college
                $employeeEducation[] = [
                    'honors_received' => $data['honor_of_school_college'] ?? 'N/A',
                    'degree_earned_of_school' => $data['college_degree_earned_of_school'] ?? 'N/A',
                    'year_graduated' => 'N/A',
                    'period_attendance_from' => 'N/A',
                    'period_attendance_to' => 'N/A',
                    'education' => $data['college_education'] ?? 'N/A',
                    'type' => EmployeeEducationType::COLLEGE,
                    'name' =>  $data['college_name'] ?? 'N/A',
                ];

                //vocational
                $employeeEducation[] = [
                    'honors_received' => $data['honor_of_school_vocational'] ?? 'N/A',
                    'degree_earned_of_school' => $data['vocationalcourse_degree_earned_of_school'] ?? 'N/A',
                    'year_graduated' => 'N/A',
                    'period_attendance_from' => 'N/A',
                    'period_attendance_to' => 'N/A',
                    'education' => $data['vocationalcourse_education'] ?? 'N/A',
                    'type' => EmployeeEducationType::VOCATIONAL,
                    'name' =>  $data['vocationalcourse_name'] ?? 'N/A',
                ];
                //father information
                $employeeRelatedPerson[] = [
                    'relationship',
                    'type' => EmployeeRelatedPersonType::FATHER,
                    'relationship' => EmployeeRelatedPersonType::FATHER,
                    'name' => $data['father_name'] ?? 'N/A',
                    'date_of_birth' => null,
                    'street' => 'N/A',
                    'brgy' => 'N/A',
                    'city' => 'N/A',
                    'zip' => 'N/A',
                    'province' => 'N/A',
                    'occupation' => 'N/A',
                    'contact_no' => 'N/A',
                ];
                //contact related information
                $employeeRelatedPerson[] = [
                    'relationship',
                    'type' => EmployeeRelatedPersonType::CONTACT_PERSON,
                    'relationship' => $data['person_to_contact_relationship'],
                    'name' => $data['person_to_contact_name'] ?? 'N/A',
                    'date_of_birth' => null,
                    'street' => $data['person_to_contact_street'] ?? 'N/A',
                    'brgy' => $data['person_to_contact_brgy' ?? 'N/A'] ?? 'N/A',
                    'city' => $data['person_to_contact_city'] ?? 'N/A',
                    'zip' => $data['person_to_contact_zip'] ?? 'N/A',
                    'province' => $data['person_to_contact_province'] ?? 'N/A',
                    'occupation' => $data['person_to_contact_no'] ?? 'N/A',
                    'contact_no' => 'N/A',
                ];
                //mother information
                $employeeRelatedPerson[] = [
                    'relationship',
                    'type' => EmployeeRelatedPersonType::MOTHER,
                    'relationship' => EmployeeRelatedPersonType::MOTHER,
                    'name' => $data['mother_name'] ?? 'N/A',
                    'date_of_birth' => null,
                    'street' => 'N/A',
                    'brgy' => 'N/A',
                    'city' => 'N/A',
                    'zip' => 'N/A',
                    'province' => 'N/A',
                    'occupation' => 'N/A',
                    'contact_no' => 'N/A',
                ];
                //spouse information
                $employeeRelatedPerson[] = [
                    'relationship',
                    'type' => EmployeeRelatedPersonType::SPOUSE,
                    'relationship' => EmployeeRelatedPersonType::SPOUSE,
                    'name' => $data['spouse_name'] ?? 'N/A',
                    'date_of_birth' => $data['spouse_datebirth'],
                    'street' => 'N/A',
                    'brgy' => 'N/A',
                    'city' => 'N/A',
                    'zip' => 'N/A',
                    'province' => 'N/A',
                    'occupation' => $data['spouse_occupation'] ?? 'N/A',
                    'contact_no' => $data['spouse_contact_no'] ?? 'N/A',
                ];

                //master studies
                $studies[] = [
                    'title' => $data['master_thesis_name'] ?? 'N/A',
                    'date' => $data['master_thesis_date'],
                    'type' => EmployeeStudiesType::MASTER,
                ];
                //doctorate studies
                $studies[] = [
                    'title' => $data['doctorate_desertation_name'] ?? 'N/A',
                    'date' => $data['doctorate_desertation_date'],
                    'type' => EmployeeStudiesType::DOCTOR,
                ];
                //professional studies
                $studies[] = [
                    'title' => $data['professional_license_name'] ?? 'N/A',
                    'date' => $data['professional_license_date'],
                    'type' => EmployeeStudiesType::PROFESSIONAL,
                ];

                //externalEmployee
                $externalEmployee[] = [
                    'position_title' => 'N/A',
                    'company_name' => $data['company'],
                    'salary' => 'N/A',
                    'status_of_appointment' => 'N/A',
                    'date_from' => 'N/A',
                    'date_to' => 'N/A',
                ];

                //eligibility
                $eligibility = [
                    'program_module' => 'N/A',
                    'certificate_lvl' => 'N/A',
                    'status' => 'N/A',
                    'cert_exp_date' => 'N/A',
                ];

                $employee->company_employments()->create($data);
                $employee->employee_externalwork()->create($externalEmployee);
                //$employee->employment_records()->create($employeeRecord);
                $employee->employee_address()->create($address_pre);
                $employee->employee_address()->create($address_per);
                $employee->employee_affiliation()->create($affiliation);
                //$employee->employee_eligibility()->create($eligibility);
                foreach ($employeeRelatedPerson as $data) {
                    $employee->employee_related_person()->create($data);
                }
                foreach ($employeeEducation as $data) {
                    $employee->employee_education()->create($data);
                }
                foreach ($studies as $data) {
                    $employee->employee_studies()->create($data);
                }
            }
        }
        return response()->json([
            'message' => 'Done save data',
            'data' => [],
        ]);
    }
}
