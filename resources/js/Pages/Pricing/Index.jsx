import MainLayout from '@/Layouts/MainLayout';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';

function formatDollars(cents) {
  return (cents / 100).toLocaleString('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: cents % 100 === 0 ? 0 : 2,
  });
}

export default function Index({ plans }) {
  const [cadence, setCadence] = useState('quarterly');
  const isQuarterly = cadence === 'quarterly';

  return (
    <MainLayout>
      <Head title="Pricing" />

      <div className="mx-auto max-w-5xl px-4 py-12">
        <h1 className="text-center text-3xl font-bold">
          Choose the plan that fits your routine
        </h1>
        <p className="mt-2 text-center text-sm text-neutral-400">
          A licensed provider will review your assessment and determine whether
          treatment is appropriate.
        </p>

        <div className="mt-8 flex justify-center">
          <div className="inline-flex rounded-md border border-neutral-700 bg-neutral-900 p-1">
            {['quarterly', 'monthly'].map((option) => (
              <button
                key={option}
                type="button"
                onClick={() => setCadence(option)}
                className={
                  'rounded px-4 py-1.5 text-xs font-semibold uppercase tracking-wide ' +
                  (cadence === option
                    ? 'bg-white text-neutral-900'
                    : 'text-neutral-400')
                }
              >
                {option}
                {option === 'quarterly' && (
                  <span className="ml-1.5 rounded bg-emerald-400/20 px-1.5 py-0.5 text-[10px] text-emerald-400">
                    Save $20/mo
                  </span>
                )}
              </button>
            ))}
          </div>
        </div>

        <div className="mt-8 grid gap-4 sm:grid-cols-3">
          {plans.map((plan) => {
            const priceCents = isQuarterly
              ? plan.quarterly_price_cents / 3
              : plan.monthly_price_cents;
            const perDoseCents = priceCents / plan.doses_per_month;

            return (
              <div
                key={plan.id}
                className={
                  'flex flex-col rounded-lg border p-6 ' +
                  (plan.is_recommended
                    ? 'border-emerald-400 bg-emerald-950/20'
                    : 'border-neutral-800 bg-neutral-900')
                }
              >
                <div className="flex items-center justify-between">
                  <span className="rounded border border-neutral-700 px-2 py-0.5 text-xs text-neutral-400">
                    {plan.doses_per_month} doses / month
                  </span>
                  {plan.is_recommended && (
                    <span className="rounded border border-emerald-400/40 px-2 py-0.5 text-xs font-semibold uppercase text-emerald-400">
                      Recommended
                    </span>
                  )}
                </div>

                <h2 className="mt-4 text-2xl font-bold">{plan.name}</h2>
                <p className="text-sm text-neutral-400">{plan.tagline}</p>

                <div className="mt-6">
                  <div className="text-3xl font-bold">
                    {formatDollars(priceCents)}
                    <span className="text-base font-normal text-neutral-400">
                      /mo
                    </span>
                  </div>
                  <p className="text-xs text-neutral-500">
                    {formatDollars(perDoseCents)} per dose
                  </p>
                </div>

                {isQuarterly && (
                  <div className="mt-4 border-t border-neutral-800 pt-4 text-xs text-neutral-500">
                    Billed {formatDollars(plan.quarterly_price_cents)} every 3
                    months
                    <p className="text-emerald-400">Save $60 per quarter</p>
                  </div>
                )}

                <ul className="mt-4 flex-1 space-y-1.5 text-sm text-neutral-300">
                  <li>{plan.doses_per_month} doses every month</li>
                  <li>Same VibeRX formulation</li>
                  <li>Provider review included</li>
                  <li>Manage through patient portal</li>
                </ul>

                <Link
                  href={route('state.show', {
                    plan: plan.id,
                  })}
                  className={
                    'mt-6 block rounded-md py-2.5 text-center text-sm font-semibold uppercase tracking-wide ' +
                    (plan.is_recommended
                      ? 'bg-emerald-300 text-neutral-900 hover:bg-emerald-200'
                      : 'border border-neutral-700 text-neutral-200 hover:bg-neutral-800')
                  }
                >
                  Get Started
                </Link>
              </div>
            );
          })}
        </div>
      </div>
    </MainLayout>
  );
}
