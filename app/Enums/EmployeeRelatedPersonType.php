<?php
namespace App\Enums;

enum EmployeeRelatedPersonType:string
{
    case MOTHER = "Mother";
    case FATHER = "Father";
    case GUARDIAN = "Guardian";
    case CHILD = "Child";
}
