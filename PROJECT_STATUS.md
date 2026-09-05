# VibeRx — Project Status

_Last updated: 2026-09-05_

VibeRx is a Laravel 13 (PHP 8.3) + Inertia.js + React 18 + Tailwind telehealth
prescription/assessment funnel, using passwordless email OTP auth and Stripe
(via Cashier) for payment authorization.

## Done

- **Auth (passwordless OTP)**: register/login via `OtpAuthController`, `EmailOtp`
  model + migration, `OtpService`.
- **Pricing / Plan selection**: `PricingController`, `PlanSelectionController`,
  `Plan` model + migration, `Pricing/Index.jsx`, `Plans/Select.jsx`.
- **State gate**: `StateGateController` + `StateGate/Show.jsx` (pre-funnel
  eligibility check by state).
- **Orders**: `Order` model + migration, tracks `current_step`, `status`,
  billing cadence, Stripe payment method fields.
- **Assessment step**: `AssessmentController`, `Assessment` model + migration,
  `AssessmentProgressService`, `Assessment/Show.jsx`.
- **Identity/KYC step**: `IdentityVerificationController`,
  `IdentityVerification` model + migration (incl. rejection reason),
  `DocumentOcrService`, `Identity/Verify.jsx`.
- **Shipping step**: `ShippingAddressController`, `ShippingAddress` model +
  migration, `Shipping/Address.jsx`.
- **Payment step**: `PaymentController`, `PaymentService`, Stripe SetupIntent
  flow (authorize card, not captured yet), Cashier customer/subscription
  migrations installed, `Payment/Method.jsx`.
- **Review/submission step**: `ReviewController`, `Review/Show.jsx` — shows
  summary, submits order to `under_review` status.
- **Profile management**: `ProfileController`, `Profile/Edit.jsx` (+ partials
  for update/delete).
- **Funnel step enum**: `FunnelStep`, `OrderStatus`, `AssessmentStatus`,
  `IdentityVerificationStatus`, `BillingCadence`, `DocumentType`,
  `OtpVerificationResult` enums centralize funnel state.

## Not started / remaining

- **Admin/provider review panel**: no controller, route, or UI exists for a
  provider to approve / request-more-info / reject a submitted order. This is
  the step after `ReviewController::store()` sets status to `under_review` —
  currently nothing acts on that status.
- **Payment capture on approval**: `PaymentService` only creates SetupIntents
  and attaches payment methods (authorize-only). No logic yet captures the
  charge when a provider approves, or releases the authorization on rejection.
- **"Needs info" messaging thread**: no `Message`/thread model or UI for
  provider ↔ patient communication when a provider needs more information.
- **Dashboard**: `Dashboard.jsx` exists but is the default Breeze placeholder
  — no patient order-status view, no provider queue view.
- **Tests**: no feature/unit tests found yet for any of the funnel
  controllers, services, or the OTP auth flow.
- **Notifications**: no email/SMS notifications for OTP delivery status, order
  status changes, or provider decisions (beyond the OTP mail template seen in
  logs).

## Known conventions (see `.ai/rules` and CLAUDE.md)

- Controllers stay thin; business logic lives in `app/Services`.
- Login never checks identity-verification status; register always does;
  Assessment entry re-checks identity status.
