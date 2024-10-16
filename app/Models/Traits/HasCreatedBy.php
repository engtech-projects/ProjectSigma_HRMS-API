<?php

namespace App\Models\Traits;

use App\Models\Users;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreatedBy
{
    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(Users::class, "created_by", "id");
    }

    public function getCreatedByUserNameAttribute()
    {
        return $this->created_by_user->employee?->fullname_first ?? $this->created_by_user->name;
    }
}
