<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeRelatedperson extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $appends = ['age', 'address'];

    protected $casts = [
        'date_of_birth' => 'datetime:Y-m-d',
    ];

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

    public function getAgeAttribute()
    {
        $a = $this->date_of_birth;
        if ($a) {
            return Carbon::createFromFormat("ymd", $a->format('ymd'))->age;
        }
        return null;
    }
    protected function getAddressAttribute()
    {
        return implode(", ", array($this->street, $this->brgy, $this->city, $this->zip, $this->province));
    }
    protected function getNameBdayAttribute()
    {
        $bday = $this->date_of_birth;
        if ($bday) {
            $bday = Carbon::parse($this->date_of_birth)->format('F j, Y');
        } else {
            $bday = "Birthday N/A";
        }
        return implode(" - ", array($this->name, $bday));
    }
}
