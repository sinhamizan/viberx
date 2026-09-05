<?php

namespace App\Models;

use App\Enums\BillingCadence;
use App\Enums\FunnelStep;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id',
    'plan_id',
    'billing_cadence',
    'status',
    'current_step',
    'submitted_at',
])]
class Order extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'billing_cadence' => BillingCadence::class,
            'status' => OrderStatus::class,
            'current_step' => FunnelStep::class,
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    public function shippingAddress(): HasOne
    {
        return $this->hasOne(ShippingAddress::class);
    }
}
