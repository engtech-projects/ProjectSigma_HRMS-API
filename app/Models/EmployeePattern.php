<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class EmployeePattern extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

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
