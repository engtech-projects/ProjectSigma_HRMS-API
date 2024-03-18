<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class ManpowerRequestStatus extends Enum
{
    const PENDING = "Pending";
    const APPROVED = "Approved";
    const DISAPPROVED = "Disapproved";
    const FILLED = "Filled";
    const HOLD = "Hold";
    const CANCELLED = "Cancelled";
}
