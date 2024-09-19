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
}
