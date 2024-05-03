<?php

namespace App\Models\Traits;

use App\Enums\PersonelAccessForm;
use Illuminate\Database\Eloquent\Builder;

trait StatusScope
{
    public function scopeApproved(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_APPROVED);
    }

    public function scopePending(Builder $query): void
    {
        $query->where('request_status', PersonelAccessForm::REQUESTSTATUS_PENDING);
    }
}
