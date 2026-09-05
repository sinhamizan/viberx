import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';

function formatDollars(cents) {
    return (cents / 100).toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: cents % 100 === 0 ? 0 : 2,
    });
}

export default function Select({ plans, preselectedPlanId }) {
    const { data, setData, post, processing } = useForm({
        plan_id:
            preselectedPlanId ??
            plans.find((plan) => plan.is_recommended)?.id ??
            plans[0]?.id,
        billing_cadence: 'quarterly',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('plans.store'));
    };

    const isQuarterly = data.billing_cadence === 'quarterly';

    return (
        <GuestLayout>
            <Head title="Choose your plan" />

            <h1 className="text-center text-2xl font-bold text-white">
                Choose the plan that fits your routine
            </h1>
            <p className="mt-2 text-center text-sm text-neutral-400">
                Your selection is not a prescription. A licensed provider will
                review your assessment and determine whether treatment is
                appropriate.
            </p>

            <div className="mt-6 flex justify-center">
                <div className="inline-flex rounded-md border border-neutral-700 bg-neutral-800 p-1">
                    {['quarterly', 'monthly'].map((cadence) => (
                        <button
                            key={cadence}
                            type="button"
                            onClick={() => setData('billing_cadence', cadence)}
                            className={
                                'rounded px-4 py-1.5 text-xs font-semibold uppercase tracking-wide ' +
                                (data.billing_cadence === cadence
                                    ? 'bg-white text-neutral-900'
                                    : 'text-neutral-400')
                            }
                        >
                            {cadence}
                        </button>
                    ))}
                </div>
            </div>

            <form onSubmit={submit} className="mt-6 space-y-3">
                {plans.map((plan) => {
                    const priceCents = isQuarterly
                        ? plan.quarterly_price_cents / 3
                        : plan.monthly_price_cents;
                    const selected = data.plan_id === plan.id;

                    return (
                        <label
                            key={plan.id}
                            className={
                                'block cursor-pointer rounded-md border p-4 transition ' +
                                (selected
                                    ? 'border-emerald-400 bg-emerald-950/30'
                                    : 'border-neutral-700 bg-neutral-800')
                            }
                        >
                            <input
                                type="radio"
                                name="plan_id"
                                value={plan.id}
                                checked={selected}
                                onChange={() => setData('plan_id', plan.id)}
                                className="sr-only"
                            />

                            <div className="flex items-center justify-between">
                                <div>
                                    <div className="flex items-center gap-2">
                                        <span className="text-lg font-bold text-white">
                                            {plan.name}
                                        </span>
                                        {plan.is_recommended && (
                                            <span className="rounded border border-emerald-400/40 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-emerald-400">
                                                Recommended
                                            </span>
                                        )}
                                    </div>
                                    <p className="text-sm text-neutral-400">
                                        {plan.tagline} · {plan.doses_per_month}{' '}
                                        doses/month
                                    </p>
                                </div>

                                <div className="text-right">
                                    <div className="text-xl font-bold text-white">
                                        {formatDollars(priceCents)}
                                        <span className="text-sm font-normal text-neutral-400">
                                            /mo
                                        </span>
                                    </div>
                                    {isQuarterly && (
                                        <p className="text-xs text-neutral-500">
                                            Billed{' '}
                                            {formatDollars(
                                                plan.quarterly_price_cents,
                                            )}{' '}
                                            every 3 months
                                        </p>
                                    )}
                                </div>
                            </div>
                        </label>
                    );
                })}

                <button
                    type="submit"
                    disabled={processing || !data.plan_id}
                    className="w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
                >
                    Continue
                </button>
            </form>
        </GuestLayout>
    );
}
