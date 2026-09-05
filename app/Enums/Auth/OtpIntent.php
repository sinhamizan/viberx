<?php

namespace App\Enums\Auth;

enum OtpIntent: string
{
    case Register = 'register';
    case Login = 'login';
}
