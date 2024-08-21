<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Users extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $appends = [
        "accessibility_names"
    ];

    protected $fillable = [
        'id',
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

    public function Employee(): BelongsTo
    {
        return $this->BelongsTo(Employee::class);
    }

    public function getAccessibilityNamesAttribute()
    {
        return Accessibilities::whereIn("id", $this->accessibilities)->get()->pluck("accessibilities_name");
    }
}
