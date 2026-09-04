<?php

namespace App\Enums;

enum OtpVerificationResult
{
    case Verified;
    case NotFound;
    case Expired;
    case TooManyAttempts;
    case InvalidCode;
}
