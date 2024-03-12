<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmployeeRelatedperson extends Model
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    protected $appends = ['age'];

    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
    ];

    public function getAgeAttribute()
    {
        $a = $this->date_of_birth;
        if($a){
            return Carbon::createFromFormat("ymd", $a->format('ymd'))->age;
        }
        return null;
    }
    // protected function age(): Attribute
    // {
    //     return new Attribute(
    //         get: fn () => Carbon::createFromFormat("ymd", $this->date_of_birth->format('ymd'))->age,
    //     );
    // }

    protected $fillable = [
        'id',
        'employee_id',
        'relationship',
        'type',
        'name',
        'date_of_birth',
        'street',
        'brgy',
        'city',
        'zip',
        'province',
        'occupation',
        'contact_no',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }


}
