import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('login'));
    };

    return (
        <GuestLayout>
            <Head title="Sign in" />

            <h1 className="text-center text-2xl font-bold text-white">
                Sign in to your account
            </h1>
            <p className="mt-2 text-center text-sm text-neutral-400">
                Enter your email and we&apos;ll send a one-time code to sign
                you in — no password required.
            </p>

            {status && (
                <div className="mt-4 text-center text-sm font-medium text-emerald-400">
                    {status}
                </div>
            )}

            <form onSubmit={submit} className="mt-8 space-y-4">
                <div>
                    <label
                        className="block text-xs font-medium uppercase tracking-wide text-neutral-400"
                        htmlFor="email"
                    >
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full rounded-md border-neutral-700 bg-neutral-800 text-white placeholder-neutral-500 shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
                        placeholder="you@example.com"
                        autoComplete="username"
                        autoFocus
                        onChange={(e) => setData('email', e.target.value)}
                    />
                    <InputError message={errors.email} className="mt-2" />
                </div>

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
                >
                    Sign in
                </button>

                <p className="text-center text-sm text-neutral-400">
                    Don&apos;t have an account?{' '}
                    <Link
                        href={route('register')}
                        className="font-semibold text-white underline"
                    >
                        Sign up
                    </Link>
                </p>
            </form>
        </GuestLayout>
    );
}
