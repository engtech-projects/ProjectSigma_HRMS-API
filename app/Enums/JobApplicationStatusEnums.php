<?php

namespace App\Enums;

enum JobApplicationStatusEnums: string
{
    case CONTACT_EXTENDED = "Contact Extended";
    case PENDING = "Pending";
    case AVAILABLE = "Available";
    case PROCESSING = "Processing";
    case INTERVIEWED = "Interviewed";
    case REJECTED = "Rejected";
    case FOR_HIRING = "For Hiring";
    case NOT_AVAILABLE = "Not Available";
    case HIRED = "Hired";
    case TESTED = "Test";
    case REFERENCE_CHECK = "Reference Checking";
    case MEDICAL_EXAM = "Medical Examination";
    case CONTRACT_SIGNED = "Contract Signed";
}
