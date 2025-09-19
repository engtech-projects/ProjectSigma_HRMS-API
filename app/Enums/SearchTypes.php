<?php

namespace App\Enums;

enum SearchTypes: string
{
    case ALLEMPLOYEES = "AllEmployees";
    case WITHACCOUNTS = "WithAccounts";
    case NOACCOUNTS = "NoAccounts";

    case NEWHIRE = "NewHire";
    case FORHIRE = "ForHire";
}
