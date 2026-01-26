<?php

namespace App\Models\Traits;

use App\Enums\RequestStatuses;
use Illuminate\Database\Eloquent\Builder;

trait StatusScope
{
    public function scopeApproved(Builder $query): void
    {
        $query->where('request_status', RequestStatuses::APPROVED);
    }

    public function scopePending(Builder $query): void
    {
        $query->where('request_status', RequestStatuses::PENDING);
    }
}
