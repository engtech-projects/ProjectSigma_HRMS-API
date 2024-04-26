<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PatternRequest extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'employee_face_pattern';

    protected $fillable = [
        'employee_id',
        'patterns',
        'created_at',
        'updated_at',
    ];
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, "id", "employee_id");
    }

}
