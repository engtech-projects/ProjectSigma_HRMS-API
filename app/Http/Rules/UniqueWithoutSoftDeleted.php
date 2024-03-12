<?php

namespace App\Http\Rules;

use App\Models\SalaryGradeLevel;
use Illuminate\Contracts\Validation\Rule;

class UniqueWithoutSoftDeleted implements Rule
{

    protected $table;
    protected $column;

    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
    }
    public function passes($attribute, $value)
    {
        return SalaryGradeLevel::where($this->column, $value)->whereNull('deleted_at')->where('id', '!=', request()->id)->count() === 0;
    }
    public function message()
    {
        return 'Salary grade level has already been taken.';
    }
}
