<?php

namespace App\Http\Services;
use App\Models\PatternRequest;

class EmployeePatternService
{
    protected $patternRequest;

    public function __construct(PatternRequest $patternRequest)
    {
        $this->patternRequest = $patternRequest;
    }

    public function getAll()
    {
        return $this->patternRequest->all();
    }

    public function getPaginate($q)
    {
        return $this->patternRequest->paginate($q);
    }

    public function insert($validatedData)
    {
        $ifExist = $this->patternRequest->where('employee_id',$validatedData['employee_id'])->first();
        if ($ifExist)
        {
            $this->patternRequest->where('employee_id',$validatedData['employee_id'])->update($validatedData);
            return true;
        }else {
            $this->patternRequest->fill($validatedData);
            if ($this->patternRequest->save())
            {
                return true;
            }else{
                return false;
            }
        }

    }
    public function getPatternByEmployeeID($id = null)
    {
        return $this->patternRequest->find($id)->first();
    }
    public function comparePattern($needle, $stack)
    {
        return null;
    }
}
