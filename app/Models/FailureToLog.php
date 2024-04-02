<?php

namespace App\Models;

use App\Traits\HasApproval;
use App\Enums\AttendanceLogType;
use App\Enums\PersonelAccessForm;
use App\Models\Traits\HasEmployee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FailureToLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasEmployee;
    use HasApproval;

    protected $fillable = [
        'date',
        'time',
        'log_type',
        'reason',
        'approvals',
        'employee_id',
    ];
    protected $casts = [
        'date' => 'date:Y-m-d',
        'time' => 'date:H:i:s',
        'log_type' => AttendanceLogType::class,
        'approvals' => 'array',
        'employee_id' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = auth()->user()->id;
        });
    }

    public function completeRequestStatus()
    {
        $this->request_status = PersonelAccessForm::REQUESTSTATUS_APPROVED;
        $this->save();
        $this->refresh();
    }


    public function denyRequestStatus()
    {

        $this->request_status = PersonelAccessForm::REQUESTSTATUS_DISAPPROVED;
        $this->save();
        $this->refresh();
    }

    public function requestStatusCompleted(): bool
    {
        if ($this->request_status == PersonelAccessForm::REQUESTSTATUS_APPROVED) {
            return true;
        }
        return false;
    }

    public function requestStatusEnded(): bool
    {
        if (
            in_array(
                $this->request_status,
                [
                    PersonelAccessForm::REQUESTSTATUS_DISAPPROVED,
                    PersonelAccessForm::REQUESTSTATUS_FILLED,
                    PersonelAccessForm::REQUESTSTATUS_HOLD,
                    PersonelAccessForm::REQUESTSTATUS_CANCELLED,
                ]
            )
        ) {
            return true;
        }
        return false;
    }
}
