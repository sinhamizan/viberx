<?php

namespace App\Models;

use App\Enums\DocumentType;
use App\Enums\IdentityVerificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'legal_first_name',
    'legal_last_name',
    'document_type',
    'document_number',
    'document_expiry_date',
    'front_photo_path',
    'back_photo_path',
    'status',
    'rejection_reason',
    'verified_at',
])]
class IdentityVerification extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'document_type' => DocumentType::class,
            'document_expiry_date' => 'date',
            'status' => IdentityVerificationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
