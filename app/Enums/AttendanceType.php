<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

enum AttendanceType: string
{
    case MANUAL = "Manual";
    case FINGERPRINT = "Fingerprint";
    case FACIAL = "Facial";
    case QR_CODE = "QR Code";
    case PASSWORD = "Password";
    case BIOMETRIC_MACHINE_FACE = "Biometric_Machine_Face";
    case BIOMETRIC_MACHINE_FINGER = "Biometric_Machine_Finger";
}
