<?php

namespace App\Enums;

enum ScheduleGroupType: string
{
    case DEPARTMENT = "department";
    case PROJECT = "project";
    case EMPLOYEE = "employee";
}
