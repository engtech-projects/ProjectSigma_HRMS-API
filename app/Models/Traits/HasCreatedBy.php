<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreatedBy
{
    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by", "id");
    }

    public function getCreatedByUserNameAttribute()
    {
        return $this->created_by_user->employee?->fullname_last ?? ($this->created_by_user?->name ?? 'USER NOT FOUND');
    }
}
