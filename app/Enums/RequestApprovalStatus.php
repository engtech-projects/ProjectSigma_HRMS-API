<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class RequestApprovalStatus extends Enum
{
    const APPROVED = 'Approved';
    const PENDING = "Pending";
    const DENIED = "Denied";

}
