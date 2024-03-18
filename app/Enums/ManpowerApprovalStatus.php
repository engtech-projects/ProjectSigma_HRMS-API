<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

final class ManpowerApprovalStatus extends Enum
{
    const APPROVED = 'Approved';
    const PENDING = "Pending";
    const DENIED = "Denied";

}
