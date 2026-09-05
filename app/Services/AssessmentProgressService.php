<?php

namespace App\Services;

use App\Enums\AssessmentStatus;
use App\Models\Assessment;
use App\Models\Order;

class AssessmentProgressService
{
    /**
     * The assessment's sub-sections, in the order they're presented.
     *
     * @var list<string>
     */
    public const SECTIONS = [
        'personal_info',
        'medical_history',
        'medications',
        'allergies',
        'prior_treatments',
        'health_conditions',
        'goals',
    ];

    /**
     * The order's assessment, creating one with an explicit Pending status
     * if it doesn't exist yet. (Relying on the database column's default
     * would leave the in-memory model's `status` attribute unset.)
     */
    public function findOrCreateFor(Order $order): Assessment
    {
        return $order->assessment ?? $order->assessment()->create([
            'status' => AssessmentStatus::Pending,
        ]);
    }
}
