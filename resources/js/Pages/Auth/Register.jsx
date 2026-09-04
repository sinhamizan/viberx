import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

const fieldClasses =
    'mt-1 block w-full rounded-md border-neutral-700 bg-neutral-800 text-white placeholder-neutral-500 shadow-sm focus:border-emerald-400 focus:ring-emerald-400';

const labelClasses =
    'block text-xs font-medium uppercase tracking-wide text-neutral-400';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        terms: false,
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('register'));
    };

    return (
        <GuestLayout>
            <Head title="Sign up" />

            <h1 className="text-center text-2xl font-bold text-white">
                Sign up your account
            </h1>
            <p className="mt-2 text-center text-sm text-neutral-400">
                Enter your basic information. We&apos;ll send a one-time code
                to verify your email — no password required.
            </p>

            <form onSubmit={submit} className="mt-8 space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className={labelClasses} htmlFor="first_name">
                            Legal First Name
                        </label>
                        <input
                            id="first_name"
                            name="first_name"
                            value={data.first_name}
                            className={fieldClasses}
                            placeholder="First name"
                            autoComplete="given-name"
                            autoFocus
                            onChange={(e) =>
                                setData('first_name', e.target.value)
                            }
                        />
                        <InputError
                            message={errors.first_name}
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <label className={labelClasses} htmlFor="last_name">
                            Legal Last Name
                        </label>
                        <input
                            id="last_name"
                            name="last_name"
                            value={data.last_name}
                            className={fieldClasses}
                            placeholder="Last name"
                            autoComplete="family-name"
                            onChange={(e) =>
                                setData('last_name', e.target.value)
                            }
                        />
                        <InputError
                            message={errors.last_name}
                            className="mt-2"
                        />
                    </div>
                </div>

                <div>
                    <label className={labelClasses} htmlFor="email">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className={fieldClasses}
                        placeholder="you@example.com"
                        autoComplete="username"
                        onChange={(e) => setData('email', e.target.value)}
                    />
                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div>
                    <label className={labelClasses} htmlFor="phone">
                        Mobile Phone (Optional)
                    </label>
                    <input
                        id="phone"
                        name="phone"
                        value={data.phone}
                        className={fieldClasses}
                        placeholder="Type your mobile number"
                        autoComplete="tel"
                        onChange={(e) => setData('phone', e.target.value)}
                    />
                    <InputError message={errors.phone} className="mt-2" />
                </div>

                <label className="flex items-start gap-2 pt-2 text-sm text-neutral-400">
                    <input
                        type="checkbox"
                        checked={data.terms}
                        onChange={(e) => setData('terms', e.target.checked)}
                        className="mt-0.5 rounded border-neutral-700 bg-neutral-800 text-emerald-400 focus:ring-emerald-400"
                    />
                    <span>
                        I agree to the{' '}
                        <span className="text-emerald-400 underline">
                            Terms &amp; Condition
                        </span>{' '}
                        and{' '}
                        <span className="text-emerald-400 underline">
                            Privacy Policy
                        </span>
                        .
                    </span>
                </label>
                <InputError message={errors.terms} />

                <button
                    type="submit"
                    disabled={processing || !data.terms}
                    className="w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
                >
                    Sign up
                </button>

                <p className="text-center text-sm text-neutral-400">
                    Already have an account?{' '}
                    <Link
                        href={route('login')}
                        className="font-semibold text-white underline"
                    >
                        Sign in
                    </Link>
                </p>
            </form>
        </GuestLayout>
    );
}
