<?php
namespace App\Enums;

enum UpdateTypesOnUser:string
{
    case PASSWORD = 'password';
    case NAME = 'name';
    case EMAIL = 'email';
}

