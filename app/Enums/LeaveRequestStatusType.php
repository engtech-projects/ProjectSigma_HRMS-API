<?php

namespace App\Enums;

enum LeaveRequestStatusType: string
{
    case APPROVED = 'Approved';
    case PENDING = "Pending";
    case DENIED = "Denied";
    case RELEASED = "Released";
    case FILLED = "Filled";
    case HOLD = "Hold";
    case CANCELLED = "Cancelled";
    case DISAPPROVED = "Disapproved";
}
