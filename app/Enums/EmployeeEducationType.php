<?php

namespace App\Enums;

enum EmployeeEducationType: string
{
    case ELEMENTARY = "elementary";
    case SECONDARY = "secondary";
    case COLLEGE = "college";
    case VOCATIONAL = "vocational";
}
