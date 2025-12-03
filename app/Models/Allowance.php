<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Allowance extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'allowances';

    protected $fillable = [
        'id',
        'position_id',
        'amount',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
