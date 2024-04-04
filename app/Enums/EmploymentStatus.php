<?php

namespace App\Enums;

enum EmploymentStatus: string
{
    case PROBATIONARY = "Probationary";
    case REGULAR = "Regular";
    case PROJECT_BASED = "Project Based";
}
