<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeePersonnelActionNoticeRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation() {
        $this->merge([
            "approvals" => json_decode($this->approvals,true)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id'=> [
                "required",
                "integer",
                "exists:employees,id",
            ],
            'type'=>[
                "required",
                "string",
                'in:New Hire,Termination,Transfer,Promotion'
            ],
            'date_of_effictivity'=>[
                "required",
                "date",
            ],
            'section_department'=>[
                "required",
                "string"
            ],
            'designation_position'=>[
                "required",
                "string"
            ],
            'salary_grade'=>[
                "required",
                "string"
            ],
            'salary_grade_step'=>[
                "required",
                "string"
            ],
            'salary_type'=>[
                "required",
                "string",
                'in:Fixed Rate,Non Fixed,Monthly,Weekly'
            ],
            'hire_source'=>[
                "required",
                "string",
                'in:Internal,External'
            ],
            'work_location'=>[
                "required",
                "string",
            ],
            'new_section'=>[
                "required",
                "string",
            ],
            'new_location'=>[
                "required",
                "string",
            ],
            'new_employment_status'=>[
                "required",
                "string",
            ],
            'new_position'=>[
                "required",
                "string",
            ],
            'new_salary_grade'=>[
                "required",
                "string",
            ],
            'new_salary_grade_step'=>[
                "required",
                "string",
            ],
            'type_of_termination'=>[
                "required",
                "string",
            ],
            'reasons_for_termination'=>[
                "required",
                "string",
            ],
            'eligible_for_rehire'=>[
                "required",
                "string",
            ],
            'last_day_worked'=>[
                "required",
                "string",
            ],
            'approvals.*'=>[
                "required",
                "array",
                "required_array_keys:type,user_id,status,date_approved,remarks",
            ],
            'approvals.*.type'=>[
                "required",
                "string",
            ],
            'approvals.*.user_id'=>[
                "nullable",
                "integer",
                "exists:users,id",
            ],
            'approvals.*.status'=>[
                "required",
                "string",
            ],
            'approvals.*.date_approved'=>[
                "nullable",
                "date",
            ],
            'approvals.*.remarks'=>[
                "nullable",
                "string",
            ],
            'created_by'=> [
                "required",
                "integer",
                "exists:users,id",
            ],
        ];
    }
}
