<?php

namespace App\Enums;

enum JobApplicationStatusEnums: string
{
    case AVAILABLE = "Available";
    case PROCESSING = "Processing";
    case NOT_AVAILABLE = "Not Available";
    case HIRED = "Hired";
}
