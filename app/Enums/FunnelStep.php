<?php

namespace App\Enums;

enum FunnelStep: string
{
    case Plan = 'plan';
    case Account = 'account';
    case Assessment = 'assessment';
    case Identity = 'identity';
    case Shipping = 'shipping';
    case Payment = 'payment';
    case Review = 'review';
}
