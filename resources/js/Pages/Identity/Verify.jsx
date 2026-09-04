import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, router, useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const fieldClasses =
    'mt-1 block w-full rounded-md border-neutral-700 bg-neutral-800 text-white placeholder-neutral-500 shadow-sm focus:border-emerald-400 focus:ring-emerald-400';

const readOnlyFieldClasses =
    'mt-1 block w-full rounded-md border-neutral-800 bg-neutral-900 text-neutral-400 shadow-sm';

const labelClasses =
    'block text-xs font-medium uppercase tracking-wide text-neutral-400';

const PROCESSING_STEPS = [
    'Checking document',
    'Matching information',
    'Confirming identity',
];

const STEP_DURATION_MS = 1500;

export default function Verify({
    legalFirstName,
    legalLastName,
    email,
    phone,
    documentTypes,
    verification,
}) {
    if (verification?.status === 'verified') {
        return <VerifiedView verification={verification} />;
    }

    if (verification?.status === 'in_progress') {
        return <ProcessingView />;
    }

    return (
        <VerificationForm
            legalFirstName={legalFirstName}
            legalLastName={legalLastName}
            email={email}
            phone={phone}
            documentTypes={documentTypes}
        />
    );
}

function VerificationForm({
    legalFirstName,
    legalLastName,
    email,
    phone,
    documentTypes,
}) {
    const { data, setData, post, processing, errors } = useForm({
        legal_first_name: legalFirstName ?? '',
        legal_last_name: legalLastName ?? '',
        document_type: '',
        document_number: '',
        document_expiry_date: '',
        front_photo: null,
        back_photo: null,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('identity.store'), { forceFormData: true });
    };

    const skip = (e) => {
        e.preventDefault();
        router.post(route('identity.skip'));
    };

    return (
        <GuestLayout>
            <Head title="Verify your identity" />

            <h1 className="text-center text-2xl font-bold text-white">
                Verify your identity
            </h1>
            <p className="mt-2 text-center text-sm text-neutral-400">
                You can complete identity verification now, or skip and
                finish later during the assessment process.
            </p>

            <form onSubmit={submit} className="mt-8 space-y-4">
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className={labelClasses}>
                            Legal First Name
                        </label>
                        <input
                            value={data.legal_first_name}
                            className={fieldClasses}
                            placeholder="First name"
                            onChange={(e) =>
                                setData('legal_first_name', e.target.value)
                            }
                        />
                        <InputError
                            message={errors.legal_first_name}
                            className="mt-2"
                        />
                    </div>

                    <div>
                        <label className={labelClasses}>
                            Legal Last Name
                        </label>
                        <input
                            value={data.legal_last_name}
                            className={fieldClasses}
                            placeholder="Last name"
                            onChange={(e) =>
                                setData('legal_last_name', e.target.value)
                            }
                        />
                        <InputError
                            message={errors.legal_last_name}
                            className="mt-2"
                        />
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className={labelClasses}>Email Address</label>
                        <input
                            value={email}
                            disabled
                            className={readOnlyFieldClasses}
                        />
                    </div>

                    <div>
                        <label className={labelClasses}>
                            Mobile Number (Optional)
                        </label>
                        <input
                            value={phone ?? ''}
                            disabled
                            placeholder="Not provided"
                            className={readOnlyFieldClasses}
                        />
                    </div>
                </div>

                <p className="text-xs text-neutral-500">
                    Date of birth and address will be collected during
                    intake.
                </p>

                <div>
                    <label className={labelClasses}>Document Type</label>
                    <select
                        value={data.document_type}
                        onChange={(e) =>
                            setData('document_type', e.target.value)
                        }
                        className={fieldClasses}
                    >
                        <option value="">Select a document type</option>
                        {documentTypes.map((type) => (
                            <option key={type.value} value={type.value}>
                                {type.label}
                            </option>
                        ))}
                    </select>
                    <InputError
                        message={errors.document_type}
                        className="mt-2"
                    />
                </div>

                {data.document_type && (
                    <>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className={labelClasses}>
                                    Document No
                                </label>
                                <input
                                    value={data.document_number}
                                    className={fieldClasses}
                                    placeholder="2345-0987-2026"
                                    onChange={(e) =>
                                        setData(
                                            'document_number',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={errors.document_number}
                                    className="mt-2"
                                />
                            </div>

                            <div>
                                <label className={labelClasses}>
                                    Expiry Date
                                </label>
                                <input
                                    type="date"
                                    value={data.document_expiry_date}
                                    className={fieldClasses}
                                    onChange={(e) =>
                                        setData(
                                            'document_expiry_date',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={errors.document_expiry_date}
                                    className="mt-2"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <PhotoUpload
                                label="Document Front Photo"
                                file={data.front_photo}
                                onChange={(file) =>
                                    setData('front_photo', file)
                                }
                                error={errors.front_photo}
                            />
                            <PhotoUpload
                                label="Document Back Photo"
                                file={data.back_photo}
                                onChange={(file) =>
                                    setData('back_photo', file)
                                }
                                error={errors.back_photo}
                            />
                        </div>
                    </>
                )}

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
                >
                    Verify identity
                </button>

                <button
                    type="button"
                    onClick={skip}
                    className="w-full text-center text-sm font-semibold text-emerald-400 underline"
                >
                    Skip for now
                </button>
            </form>
        </GuestLayout>
    );
}

function PhotoUpload({ label, file, onChange, error }) {
    return (
        <div>
            <label className={labelClasses}>{label}</label>
            <label className="mt-1 flex cursor-pointer flex-col items-center justify-center rounded-md border border-dashed border-neutral-700 bg-neutral-800 px-3 py-4 text-center hover:border-emerald-400">
                {file ? (
                    <span className="flex w-full items-center justify-between text-sm text-neutral-200">
                        <span className="truncate">{file.name}</span>
                        <button
                            type="button"
                            onClick={(e) => {
                                e.preventDefault();
                                onChange(null);
                            }}
                            className="ml-2 text-neutral-400 hover:text-white"
                        >
                            &times;
                        </button>
                    </span>
                ) : (
                    <span className="text-xs text-neutral-400">
                        Click to upload document photo
                        <br />
                        Supports PDF, JPG, PNG up to 10MB
                    </span>
                )}
                <input
                    type="file"
                    accept=".pdf,.jpg,.jpeg,.png"
                    className="hidden"
                    onChange={(e) => onChange(e.target.files[0] ?? null)}
                />
            </label>
            <InputError message={error} className="mt-2" />
        </div>
    );
}

function ProcessingView() {
    const [stepIndex, setStepIndex] = useState(0);

    useEffect(() => {
        if (stepIndex >= PROCESSING_STEPS.length) {
            router.post(route('identity.confirm'));

            return;
        }

        const timer = setTimeout(
            () => setStepIndex((i) => i + 1),
            STEP_DURATION_MS,
        );

        return () => clearTimeout(timer);
    }, [stepIndex]);

    const percent = Math.round(
        (Math.min(stepIndex, PROCESSING_STEPS.length) /
            PROCESSING_STEPS.length) *
            100,
    );

    return (
        <GuestLayout>
            <Head title="Verifying your identity" />

            <h1 className="text-center text-2xl font-bold text-white">
                Verify your identity
            </h1>
            <p className="mt-2 text-center text-sm text-neutral-400">
                You can complete identity verification now, or skip and
                finish later during the assessment process.
            </p>

            <div className="mt-8 rounded-md border border-neutral-800 bg-neutral-800/50 p-5">
                <span className="inline-flex items-center rounded border border-emerald-400/40 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-emerald-400">
                    In progress
                </span>

                <h2 className="mt-3 text-sm font-semibold uppercase tracking-wide text-white">
                    Verifying your identity
                </h2>
                <p className="mt-1 text-xs text-neutral-500">
                    This usually takes about 5-10 seconds. Please keep this
                    page open.
                </p>

                <ul className="mt-4 space-y-2">
                    {PROCESSING_STEPS.map((step, index) => (
                        <li
                            key={step}
                            className={
                                'text-sm ' +
                                (index < stepIndex
                                    ? 'text-emerald-400'
                                    : 'text-neutral-500')
                            }
                        >
                            {step}
                        </li>
                    ))}
                </ul>

                <div className="mt-4 flex items-center gap-3">
                    <div className="h-1.5 flex-1 overflow-hidden rounded-full bg-neutral-700">
                        <div
                            className="h-full rounded-full bg-emerald-400 transition-all duration-500"
                            style={{ width: `${percent}%` }}
                        />
                    </div>
                    <span className="text-xs text-neutral-400">
                        {percent}%
                    </span>
                </div>
            </div>
        </GuestLayout>
    );
}

function VerifiedView({ verification }) {
    return (
        <GuestLayout>
            <Head title="Identity verified" />

            <h1 className="text-center text-2xl font-bold text-white">
                Verify your identity
            </h1>
            <p className="mt-2 text-center text-sm text-neutral-400">
                Your account and identity verification are complete.
                You&apos;re signed in and we&apos;ll take you back to where
                you started.
            </p>

            <div className="mt-8 rounded-md border border-neutral-800 bg-neutral-800/50 p-5">
                <span className="inline-flex items-center rounded border border-emerald-400/40 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-emerald-400">
                    Verified
                </span>

                <h2 className="mt-3 text-sm font-semibold uppercase tracking-wide text-white">
                    Identity Verified
                </h2>

                <dl className="mt-4 space-y-2 text-sm">
                    <div className="flex justify-between border-b border-neutral-800 pb-2">
                        <dt className="text-neutral-500">Document Type</dt>
                        <dd className="text-white">
                            {verification.documentTypeLabel}
                        </dd>
                    </div>
                    <div className="flex justify-between border-b border-neutral-800 pb-2">
                        <dt className="text-neutral-500">Legal Name</dt>
                        <dd className="text-white">
                            {verification.legalName}
                        </dd>
                    </div>
                    <div className="flex justify-between">
                        <dt className="text-neutral-500">
                            Verification Status
                        </dt>
                        <dd className="font-semibold text-emerald-400">
                            Verified
                        </dd>
                    </div>
                </dl>
            </div>

            <button
                type="button"
                onClick={() => router.visit(route('dashboard'))}
                className="mt-6 w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200"
            >
                Continue
            </button>
        </GuestLayout>
    );
}
