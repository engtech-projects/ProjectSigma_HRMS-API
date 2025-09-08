<?php

namespace App\Enums;

enum PostingStatusType: string
{
    // 'Posted','Not Posted'
    case POSTED = "Posted";
    case NOTPOSTED = "Not Posted";
}
