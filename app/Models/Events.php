<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Events extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'title',
        'event_type',
        'repetition_type',
        'with_pay',
        'with_work',
        'start_date',
        'end_date',
        'description',
        'attendance_date',
    ];

    protected $casts = [
        "start_date" => "datetime:Y-m-d",
        "end_date" => "datetime:Y-m-d",
    ];

    public function scopeBetweenDates(Builder $query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('start_date', [$dateFrom, $dateTo])
        ->orwhereBetween('end_date', [$dateFrom, $dateTo])
        ->orWhere(function ($query) use ($dateFrom, $dateTo) {
            $query->where('start_date', '<=', $dateFrom)
                  ->where('end_date', '>=', $dateTo);
        });
    }
    public function scopeWithPay(Builder $query)
    {
        return $query->where('with_pay', true);
    }
    public function scopeWithWork(Builder $query)
    {
        return $query->where('with_work', true);
    }
    public function scopeWithoutPay(Builder $query)
    {
        return $query->where('with_pay', false);
    }
    public function scopeWithoutWork(Builder $query)
    {
        return $query->where('with_work', false);
    }
}
