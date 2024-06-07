<?php

namespace App\Models;

use App\Enums\RequestStatusType;
use App\Models\PayrollDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PayrollRecord extends Model
{
    use HasFactory;
    use HasApproval;

    protected $fillable = [
        'project_id',
        'department_id',
        'payroll_type',
        'release_type',
        'payroll_date',
        'cutoff_start',
        'cutoff_end',
        'request_status',
        'approvals',
    ];
    protected $casts = [
        "approvals" => 'array'
    ];
    /**
     * The roles that belong to the PayrollRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payroll_details(): HasMany
    {
        return $this->hasMany(PayrollDetail::class)->with('deductions', 'adjustments', 'charges');
    }

    public function department(): HasOne
    {
        return $this->hasOne(Department::class, "id", "department_id");
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, "id", "project_id");
    }


    function getChargingNameAttribute() {
        if($this->project_id){
            return $this->project->project_code;
        }
        if($this->department_id){
            return $this->department->department_name;
        }
        return 'No charging found.';
    }

    public function scopeRequestStatusPending(Builder $query): void
    {
        $query->where('request_status', RequestStatusType::PENDING);
    }

    public function scopeRequestStatusApproved(Builder $query): void
    {
        $query->where('request_status', RequestStatusType::APPROVED);
    }


}
