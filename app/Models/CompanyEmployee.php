<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class CompanyEmployee extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'company_employments';

    protected $fillable = [
        'id',
        'employee_id',
        'employeedisplay_id',
        'company',
        'date_hired',
        'imidiate_supervisor',
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
}
