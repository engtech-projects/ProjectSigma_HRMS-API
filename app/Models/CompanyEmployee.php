<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CompanyEmployee extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'company_employments';

    protected $fillable = [
        'id',
        'employee_id',
        'employeedisplay_id',
        'date_hired',
        'phic_number',
        'sss_number',
        'tin_number',
        'pagibig_number',
        'status',
        'atm',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected function getEmployeeDateHiredAttribute()
    {
        $data = $this->date_hired;
        if ($data) {
            $data = Carbon::parse($this->date_hired)->format('F j, Y');
        } else {
            $data = "Date Hired N/A";
        }
        return $data;
    }
}
