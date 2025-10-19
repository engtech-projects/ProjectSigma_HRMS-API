<?php

namespace App\Enums;

enum EmployeeStudiesType: string
{
    case MASTER = "master thesis";
    case DOCTOR = "doctor dissertation";
    case PROFESSIONAL = "professional license";
}
