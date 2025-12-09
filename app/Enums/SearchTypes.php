<?php

namespace App\Enums;

enum SearchTypes: string
{
    case ALLEMPLOYEES = "AllEmployees";
    case WITHACCOUNTS = "WithAccounts";
    case NOACCOUNTS = "NoAccounts";
    case INACTIVE = "Inactive";

    case NEWHIRE = "NewHire";
    case FORHIRE = "ForHire";
}
