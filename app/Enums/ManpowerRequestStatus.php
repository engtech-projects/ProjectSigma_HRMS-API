<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class ManpowerRequestStatus extends Enum
{
    public const PENDING = "Pending";
    public const APPROVED = "Approved";
    public const DISAPPROVED = "Disapproved";
    public const FILLED = "Filled";
    public const HOLD = "Hold";
    public const CANCELLED = "Cancelled";
}
