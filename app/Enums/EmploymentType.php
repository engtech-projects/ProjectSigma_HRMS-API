<?php

namespace App\Enums;

enum EmploymentType: string
{
    case REGULAR = "Regular";
    case PROBATIONARY = "Probationary";
    case PARTTIME = "Part Time";
    case PROJECTBASED = "Project Based";
    case CONTRACTUAL = "Contractual";
}
