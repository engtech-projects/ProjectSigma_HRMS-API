<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class EmployeePattern extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'employee_face_pattern';

    protected $casts = [
        'patterns' => 'array',
    ];
    protected $fillable = [
        'employee_id',
        'patterns',
        'created_at',
        'updated_at',
    ];
    protected $with = [
        "employee",
    ];
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }
}
