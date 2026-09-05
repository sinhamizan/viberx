<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'personal_info',
    'medical_history',
    'medications',
    'allergies',
    'prior_treatments',
    'health_conditions',
    'goals',
    'status',
    'submitted_at',
])]
class Assessment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'personal_info' => 'array',
            'medical_history' => 'array',
            'medications' => 'array',
            'allergies' => 'array',
            'prior_treatments' => 'array',
            'health_conditions' => 'array',
            'goals' => 'array',
            'status' => AssessmentStatus::class,
            'submitted_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
