<?php

namespace App\Models;

use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use ModelHelpers;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'accessibilities',
        'email',
        'password',
        'type',
        "employee_id"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'accessibilities' => 'array',
    ];

    protected $appends = [
        "accessibility_names"
    ];

    /*** MODEL RELATION */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getAccessibilityNamesAttribute()
    {
        return Accessibilities::whereIn("id", $this->accessibilities)->get()->pluck("accessibilities_name");
    }
}
