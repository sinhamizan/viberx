<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'slug',
    'tagline',
    'doses_per_month',
    'monthly_price_cents',
    'quarterly_price_cents',
    'is_recommended',
    'sort_order',
])]
class Plan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_recommended' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
