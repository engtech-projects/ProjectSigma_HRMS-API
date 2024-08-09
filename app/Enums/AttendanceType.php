<?php

namespace App\Enums;

enum AttendanceType: string
{
    case ALL = 'All';
    case MANUAL = "Manual";
    case FINGERPRINT = "Fingerprint";
    case FACIAL = "Facial";
    case QR_CODE = "QR Code";
    case PASSWORD = "Password";
    case BIOMETRIC_MACHINE_FACE = "Biometric_Machine_Face";
    case BIOMETRIC_MACHINE_FINGER = "Biometric_Machine_Finger";
}
