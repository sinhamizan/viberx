<?php

namespace App\Enums;

enum IdentityVerificationStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Verified = 'verified';
    case Rejected = 'rejected';
    case Skipped = 'skipped';
}
