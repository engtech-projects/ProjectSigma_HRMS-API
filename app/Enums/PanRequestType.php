<?php

namespace App\Enums;

enum PanRequestType: string
{
    case NEW_HIRE = "New Hire";
    case TRANSFER = "Transfer";
    case PROMOTION = "Promotion";
    case TERMINATION = "Termination";
}
