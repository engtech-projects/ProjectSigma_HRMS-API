<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Allowance extends Model
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasApproval;

    protected $table = 'allowances';

    protected $casts = [
        'approvals' => 'array',
    ];

    protected $fillable = [
        'id',
        'position_id',
        'amount',
        'approvals',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

}
