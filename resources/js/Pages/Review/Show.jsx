import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

function SummaryCard({ number, title, editHref, children }) {
  return (
    <div className="rounded-md border border-neutral-800 bg-neutral-800/50 p-4">
      <div className="flex items-center justify-between">
        <h2 className="text-xs font-semibold uppercase tracking-wide text-white">
          {number}. {title}
        </h2>
        {editHref && (
          <Link
            href={editHref}
            className="text-xs font-semibold uppercase text-emerald-400 underline"
          >
            Edit
          </Link>
        )}
      </div>

      <dl className="mt-3 space-y-1.5">{children}</dl>
    </div>
  );
}

function Row({ label, value }) {
  return (
    <div className="flex justify-between text-sm">
      <dt className="text-neutral-500">{label}</dt>
      <dd className="text-right text-white">{value}</dd>
    </div>
  );
}

export default function Show({
  submitted,
  status,
  plan,
  account,
  shipping,
  payment,
  assessmentSummary,
}) {
  if (submitted) {
    return <SubmittedView status={status} />;
  }

  const { data, setData, post, processing, errors } = useForm({
    consent: false,
  });

  const submit = (e) => {
    e.preventDefault();
    post(route('review.store'));
  };

  return (
    <GuestLayout>
      <Head title="Overview & submit" />

      <h1 className="text-center text-2xl font-bold text-white">
        Overview &amp; submit
      </h1>
      <p className="mt-2 text-center text-sm text-neutral-400">
        Confirm your details and consents. No charge until approval.
      </p>

      <form onSubmit={submit} className="mt-8 space-y-3">
        <SummaryCard
          number="01"
          title="Selected Plan"
          editHref={route('plans.index')}
        >
          <Row label="Selected Plan" value={plan.name} />
          <Row
            label="Billing Cycle"
            value={
              plan.billingCadence === 'quarterly'
                ? 'Quarterly Billing (Every 3 months)'
                : 'Monthly Billing'
            }
          />
        </SummaryCard>

        <div className="grid grid-cols-2 gap-3">
          <SummaryCard
            number="02"
            title="Account Details"
            editHref={route('profile.edit')}
          >
            <Row label="Full Name" value={account.fullName} />
            <Row label="Email Address" value={account.email} />
            <Row label="Phone Number" value={account.phone} />
          </SummaryCard>

          <SummaryCard
            number="03"
            title="Shipping Address"
            editHref={route('shipping.show')}
          >
            <Row label="Delivery Address" value={shipping.addressLine1} />
            <Row
              label="City & State"
              value={`${shipping.city}, ${shipping.state}`}
            />
            <Row label="ZIP Code" value={shipping.postalCode} />
          </SummaryCard>
        </div>

        <div className="grid grid-cols-2 gap-3">
          <SummaryCard
            number="04"
            title="Payment Method"
            editHref={route('payment.show')}
          >
            <Row
              label="Card"
              value={`${payment.cardBrand} •••• ${payment.cardLastFour}`}
            />
            <Row
              label="Expiry Date"
              value={`${String(payment.cardExpMonth).padStart(2, '0')} / ${String(payment.cardExpYear).slice(-2)}`}
            />
          </SummaryCard>

          <SummaryCard
            number="05"
            title="Medical Questionnaire"
            editHref={route('assessment.show')}
          >
            <Row
              label="Reported Disclosures"
              value={assessmentSummary.reportedDisclosures}
            />
            <Row
              label="Consultation Form"
              value={assessmentSummary.consultationForm}
            />
          </SummaryCard>
        </div>

        <label className="flex items-start gap-2 pt-2 text-sm text-neutral-400">
          <input
            type="checkbox"
            checked={data.consent}
            onChange={(e) => setData('consent', e.target.checked)}
            className="mt-0.5 rounded border-neutral-700 bg-neutral-800 text-emerald-400 focus:ring-emerald-400"
          />
          <span>
            I confirm that the information above is accurate and agree to the
            VibeRX Terms of Service and Privacy Policy. I understand that my
            card will only be charged upon provider approval.
          </span>
        </label>
        <InputError message={errors.consent} />

        <button
          type="submit"
          disabled={processing || !data.consent}
          className="w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
        >
          Submit review
        </button>
      </form>
    </GuestLayout>
  );
}

function SubmittedView({ status }) {
  return (
    <GuestLayout>
      <Head title="Assessment submitted" />

      <svg
        className="mx-auto h-8 w-8 text-emerald-400"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          strokeLinecap="round"
          strokeLinejoin="round"
          strokeWidth={2}
          d="M5 13l4 4L19 7"
        />
      </svg>

      <h1 className="mt-4 text-center text-2xl font-bold text-white">
        Assessment submitted
      </h1>
      <p className="mt-2 text-center text-sm text-neutral-400">
        A licensed provider will review your submission. You have not been
        charged. We&apos;ll notify you when a decision is available.
      </p>

      <div className="mt-6 flex items-center justify-between rounded-md border border-emerald-400/30 bg-emerald-950/30 px-4 py-3">
        <div>
          <p className="text-xs uppercase tracking-wide text-neutral-500">
            Clinical Status
          </p>
          <p className="font-semibold text-white">Under Review</p>
        </div>
        <span className="rounded border border-emerald-400/40 px-2 py-0.5 text-xs font-semibold uppercase text-emerald-400">
          {status === 'under_review' ? 'Pending' : status}
        </span>
      </div>

      <div className="mt-6 grid grid-cols-2 gap-3">
        <Link
          href="/"
          className="rounded-md border border-neutral-700 py-2 text-center text-sm font-semibold uppercase tracking-wide text-neutral-300"
        >
          Go to Home
        </Link>
        <Link
          href={route('dashboard')}
          className="rounded-md bg-emerald-300 py-2 text-center text-sm font-semibold uppercase tracking-wide text-neutral-900 hover:bg-emerald-200"
        >
          Go to Portal
        </Link>
      </div>
    </GuestLayout>
  );
}
