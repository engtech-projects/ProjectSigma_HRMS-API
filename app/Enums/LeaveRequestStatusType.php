<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class LeaveRequestStatusType extends Enum
{
    public const APPROVED = 'Approved';
    public const PENDING = "Pending";
    public const DENIED = "Denied";
    public const RELEASED = "Released";
    public const FILLED = "Filled";
    public const HOLD = "Hold";
    public const CANCELLED = "Cancelled";
    public const DISAPPROVED = "Disapproved";
}
