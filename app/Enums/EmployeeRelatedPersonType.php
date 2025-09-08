<?php

namespace App\Enums;

enum EmployeeRelatedPersonType: string
{
    case MOTHER = "mother";
    case FATHER = "father";
    case CONTACT_PERSON = "contact person";
    case SPOUSE = "spouse";
    case REFERENCE = "reference";
    case GUARDIAN = "guardian";
    case CHILD = "dependent/children";
}
