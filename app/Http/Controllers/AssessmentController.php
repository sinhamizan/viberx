<?php

namespace App\Http\Controllers;

use App\Enums\AssessmentStatus;
use App\Enums\IdentityVerificationStatus;
use App\Enums\OrderStatus;
use App\Http\Requests\StoreAssessmentRequest;
use App\Services\AssessmentProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssessmentController extends Controller
{
    public function __construct(private readonly AssessmentProgressService $progress)
    {
        //
    }

    public function show(Request $request): Response|RedirectResponse
    {
        $order = $request->user()->orders()->where('status', OrderStatus::Draft)->latest()->first();

        if (! $order) {
            return to_route('plans.index');
        }

        $assessment = $this->progress->findOrCreateFor($order);

        return Inertia::render('Assessment/Show', [
            'answers' => collect(AssessmentProgressService::SECTIONS)
                ->mapWithKeys(fn (string $section) => [$section => $assessment->{$section}])
                ->all(),
        ]);
    }

    public function store(StoreAssessmentRequest $request): RedirectResponse
    {
        $order = $request->user()->orders()->where('status', OrderStatus::Draft)->latest()->firstOrFail();
        $assessment = $this->progress->findOrCreateFor($order);

        $assessment->fill($request->validated());
        $assessment->status = AssessmentStatus::Submitted;
        $assessment->submitted_at = now();
        $assessment->save();

        $identityVerified = $request->user()->identityVerifications()
            ->where('status', IdentityVerificationStatus::Verified)
            ->exists();

        return $identityVerified
            ? to_route('dashboard')
            : to_route('identity.show');
    }
}
