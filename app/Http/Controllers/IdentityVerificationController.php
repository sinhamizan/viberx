<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\IdentityVerificationStatus;
use App\Http\Requests\IdentityVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IdentityVerificationController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();
        $verification = $user->identityVerifications()->latest()->first();

        return Inertia::render('Identity/Verify', [
            'legalFirstName' => $verification->legal_first_name ?? $user->first_name,
            'legalLastName' => $verification->legal_last_name ?? $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'documentTypes' => collect(DocumentType::cases())->map(fn (DocumentType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
            'verification' => $verification ? [
                'status' => $verification->status->value,
                'documentTypeLabel' => $verification->document_type?->label(),
                'legalName' => trim("{$verification->legal_first_name} {$verification->legal_last_name}"),
            ] : null,
        ]);
    }

    public function store(IdentityVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->identityVerifications()->create([
            ...$request->safe()->except(['front_photo', 'back_photo']),
            'front_photo_path' => $request->file('front_photo')->store("identity-documents/{$user->id}"),
            'back_photo_path' => $request->file('back_photo')->store("identity-documents/{$user->id}"),
            'status' => IdentityVerificationStatus::InProgress,
        ]);

        return to_route('identity.show');
    }

    public function confirm(Request $request): RedirectResponse
    {
        $verification = $request->user()->identityVerifications()->latest()->firstOrFail();

        $verification->update([
            'status' => IdentityVerificationStatus::Verified,
            'verified_at' => now(),
        ]);

        return to_route('identity.show');
    }

    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->identityVerifications()->create([
            'legal_first_name' => $user->first_name,
            'legal_last_name' => $user->last_name,
            'status' => IdentityVerificationStatus::Skipped,
        ]);

        return to_route('dashboard');
    }
}
