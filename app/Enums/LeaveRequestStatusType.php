<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class LeaveRequestStatusType extends Enum
{
    const APPROVED = 'Approved';
    const PENDING = "Pending";
    const DENIED = "Denied";
    const RELEASED = "Released";
    const FILLED = "Filled";
    const HOLD = "Hold";
    const CANCELLED = "Cancelled";
    const DISAPPROVED = "Disapproved";
}
