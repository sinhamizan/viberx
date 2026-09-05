<?php

namespace App\Enums;

enum AssessmentStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
}
