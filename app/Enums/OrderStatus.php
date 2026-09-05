<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case UnderReview = 'under_review';
    case NeedsInfo = 'needs_info';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
