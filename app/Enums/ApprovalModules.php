<?php

namespace App\Enums;

enum ApprovalModules: string
{
    case ACCOUNTING = 'Accounting';
    case INVENTORY = 'Inventory';
    case HRMS = 'HRMS';
    case PROJECT = 'Project';
}
